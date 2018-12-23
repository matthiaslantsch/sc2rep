from __future__ import absolute_import, print_function, unicode_literals, division
import argparse, math, json, os, time
import base64
import sc2reader
import re

parser = argparse.ArgumentParser(description="Loads map specific information and tries to check if its a melee map.")
parser.add_argument('path', metavar='path', type=str, nargs=1, help="Path to the s2ma file to analyze.")
args = parser.parse_args()

map = sc2reader.load_map(args.path[0])

desc = {
	"name": map.name,
	"mapX": map.map_info.width,
	"mapY": map.map_info.height,
	"minimap": base64.b64encode(map.minimap),
	"denied": False
}

mapScript = map.archive.read_file("MapScript.galaxy")
meleeTriggers = ""
regex = r'MeleeInitResources\(\);[\s\n]+MeleeInitUnits\(\);[\s\n]+MeleeInitAI\(\);[\s\n]+MeleeInitOptions\(\);'

if mapScript is None or re.search(regex,mapScript) is None:
	desc["denied"] = True

print(json.dumps(desc))