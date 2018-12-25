from __future__ import absolute_import, print_function, unicode_literals, division
import json
import sys
import os
import sc2reader
import argparse
import time
from datetime import datetime, date
import hashlib

parser = argparse.ArgumentParser(description="Prints minimal replay data to a json string for replay identification.")
parser.add_argument('path', metavar='path', type=str, nargs=1, help="Path to the replay to serialize.")
args = parser.parse_args()
replay = sc2reader.load_replay(args.path[0], load_level=2)

repHash = str(replay.region.upper().replace("US", "NA"))
for ent in replay.entities:
	repHash += ent.name.encode("utf-8")+str(ent.toon_id)

repHash = str((replay.unix_timestamp - replay.real_length.seconds))+"-"+hashlib.md5(b""+repHash).hexdigest()

desc = {
	"loops": replay.frames,
	"repHash": repHash,
	"mapHash": replay.map_hash,
	"mapUrl": str(replay.map_file),
	"version": replay.release_string,
	"denied": False
}

print(json.dumps(desc))
