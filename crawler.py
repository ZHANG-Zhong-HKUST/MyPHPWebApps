import requests
import hashlib
import json
import tqdm
import os
import re
import functools
from bs4 import BeautifulSoup

def get_major_list(index):
    url = 'https://w5.ab.ust.hk/wcq/cgi-bin/' + str(index) + '/'
    url_sub = url + 'subject/'
    headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36'}
    r = requests.get(url, headers=headers)
    reg = '/wcq/cgi-bin/' + str(index) + '/subject/([A-Z]+)'
    ret = re.findall(reg, r.text)
    res = []
    for x in ret:
        res.append((x, url_sub + x))
    res = list(set(res))
    res.sort()
    return res

def crawl(index, generate_md5 = True):
    major_list = get_major_list(index)
    result = dict()
    md5_result = dict()
    with tqdm.tqdm(total = len(major_list)) as bar:
        for maj in major_list:
            txt = requests.get(maj[1]).text
            soup = BeautifulSoup(txt, 'html.parser')
            title = soup.find_all('h2')
            content = soup.find_all('table', {'class' : 'sections'})
            for i in range(len(title)):
                reg = '([A-Z0-9 ]+) - ([\s\S]+) \(([0-9])'
                re_ret = re.match(reg, title[i].get_text())
                if re_ret == None:
                    continue
                course_code = re_ret[1].replace(' ', '')
                course_name = re_ret[2]
                course_credit = re_ret[3]
                course_slots = []
                ret = content[i].find_all('tr', {'class' : ['newsect secteven', 'newsect sectodd', 'secteven', 'sectodd']})
                section = None
                quota = None
                avail = None
                enrol = None
                wait = None
                time = []
                venue = []
                ins = []
                for j in range(len(ret)):
                    tt = ret[j].find_all('td')
                    if 'newsect' in ret[j].attrs['class']:
                        if j > 0:
                            course_slots.append({
                                'section' : section,
                                'time' : time,
                                'venue' : venue,
                                'instructor' : ins,
                                'quota' : quota,
                                'enrol' : enrol,
                                'avail' : avail,
                                'wait' : wait
                                })
                        time = []
                        venue = []
                        ins = []
                        section = tt[0].get_text('\n', '<br>')
                        time.append(tt[1].get_text('\n', '<br>'))
                        venue.append(tt[2].get_text('\n', '<br>'))
                        ins.append(tt[3].get_text('\n', '<br>'))
                        quota = tt[4].get_text('\n', '<br>')
                        enrol = tt[5].get_text('\n', '<br>')
                        avail = tt[6].get_text('\n', '<br>')
                        wait = tt[7].get_text('\n', '<br>')
                    else:
                        time.append(tt[0].get_text('\n', '<br>'))
                        venue.append(tt[1].get_text('\n', '<br>'))
                        ins.append(tt[2].get_text('\n', '<br>'))
                    if j == len(ret) - 1:
                        course_slots.append({
                            'section' : section,
                            'time' : time,
                            'venue' : venue,
                            'instructor' : ins,
                            'quota' : quota,
                            'enrol' : enrol,
                            'avail' : avail,
                            'wait' : wait
                            })
                result[course_code] = {
                    'course_code' : course_code,
                    'course_name' : course_name,
                    'course_credit' : course_credit,
                    'course_slots' : course_slots
                }
                if generate_md5:
                    md5_result[course_code] = hashlib.md5(json.dumps(result[course_code], sort_keys = True).encode('utf-8')).hexdigest()
            bar.update(1)
    return [result, md5_result]

def localcrawl():
    file = open('result', 'r')
    js = file.read()
    dic = eval(js)
    file.close()
    return dic

def process_venue(venue):
    reg = ' \([0-9]*\)'
    venue = re.sub(reg, "", venue)
    return venue

def process_time(time):
    ret = {}
    for i in time:
        ts = re.search('[0-9]{2}:[0-9]{2}[AP]M - [0-9]{2}:[0-9]{2}[AP]M', i)
        if (ts==None):
            return {}
        ts = ts.group()
        wd = re.findall('(Mo|Tu|We|Th|Fr)', i)
        for j in wd:
            if not (ret.__contains__(j)):
                ret[j] = []
            ret[j].append(ts)
    return ret

def process_ins(ins):
    ret = []
    for i in ins:
        for j in i.split('\n'):
            ret.append(j)
    return ret


def getRoomUsage():
    rooms = {}
    res = crawl(index = 2230, generate_md5 = False)
    # res = localcrawl()
    for course in res[0]:
        course_code = res[0][course]['course_code']
        for slot in res[0][course]['course_slots']:
            venue = slot['venue'][0]
            time = slot['time']
            course_section = slot['section']
            course_ins = slot['instructor']
            course_ins = process_ins(course_ins)
            venue = process_venue(venue)
            time = process_time(time)
            if not (rooms.__contains__(venue)):
                rooms[venue] = {}
            for wd,ts in time.items():
                if not (rooms[venue].__contains__(wd)):
                    rooms[venue][wd]=[]
                for t in ts:
                    rooms[venue][wd].append((t,course_code,course_section,course_ins))
    return rooms

def getCourseList():
    ret = {}
    # res = crawl(index = 2210, generate_md5 = False)
    res = localcrawl()
    for course in res[0]:
        course_code = res[0][course]['course_code']
        course_name = res[0][course]['course_name']
        ret[course_code]=course_name
    return ret

def cmp_time(a,b):
    if time_convert(a[0][:7]) < time_convert(b[0][:7]):
        return -1
    if time_convert(a[0][:7]) == time_convert(b[0][:7]):
        return 0
    return 1

def beautify(rooms):
    tmp = {}
    for i in sorted(rooms.keys()):
        tmp[i]=rooms[i]
    for j in tmp.keys():
        for i in tmp[j].keys():
            if(i!='name'):
                tmp[j][i].sort(key=functools.cmp_to_key(cmp_time))
    for i in tmp.keys():
        tmp[i]["name"] = i
    return tmp

def time_convert(a):
    ans = 0
    if(a[-2]=='P' and int(a[0:2])!=12):
        ans += 720
    return ans+int(a[0:2])*60+int(a[3:5])

def time_contain(a,b):
    '''
    a overlap b
    '''
    tm = re.findall('[0-9]{2}:[0-9]{2}[AP]M',a)
    l1 = time_convert(tm[0])
    r1 = time_convert(tm[1])
    tm = re.findall('[0-9]{2}:[0-9]{2}[AP]M',b)
    l2 = time_convert(tm[0])
    r2 = time_convert(tm[1])
    if (r1<=l2 or r2<=l1):
        return False
    return True

def timeinterval_contain(l2, r2 ,b):
    tm = re.findall('[0-9]{2}:[0-9]{2}[AP]M',b)
    l1 = time_convert(tm[0])
    r1 = time_convert(tm[1])
    if (r1<=l2 or r2<=l1):
        return False
    return True

def get_empty(rooms, start, duration, wd):
    l2 = time_convert(start)
    r2 = l2 + duration
    ret = []
    for room in rooms.keys():
        flag = 1
        if(wd in rooms[room].keys()):
            for i in rooms[room][wd]:
                if(timeinterval_contain(l2, r2, i[0])):
                    flag = 0
        if flag:
            ret.append(room)
    return ret

def print_timetable(rooms, room):
    if not (room in rooms.keys()):
        return {}
    tmp = rooms[room]
    for i in ['Mo','Tu','We','Th','Fr']:
        tmp[i].sort(key=functools.cmp_to_key(cmp_time))
        print(i+":")
        for j in tmp[i]:
            print(j[0]+"\t"+j[1])
    return tmp

def local_rooms():
    file = open('rooms', 'r')
    js = file.read()
    dic = eval(js)
    file.close()
    return dic

def getRooms(rooms):
    ret = []
    for key in rooms.keys():
        if not (key in ret):
            ret.append(key)
    return ret

# rooms = getRoomUsage()
rooms = local_rooms()
# rooms = beautify(rooms)
json.dumps(rooms)
json.dumps(getRooms(rooms))

courses = getCourseList()


