import json, os

realpath = os.path.realpath(os.path.join(os.getcwd(), os.path.dirname(__file__)))
f = open(realpath+os.sep+'assets.json','r')
assets = json.loads(f.read())
f.close()