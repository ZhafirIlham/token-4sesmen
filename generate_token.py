import random
import string
import json
import datetime

def generate_random_token():
    return ''.join(random.choices(string.ascii_uppercase, k=6))

data = {
    "updated_at": datetime.datetime.utcnow().isoformat() + "Z",
    "set_1_token_utama": generate_random_token(),
    "set_2_token_kunci": generate_random_token()
}

with open("token.json", "w") as f:
    json.dump(data, f, indent=4)

print("Token berhasil di-generate!")