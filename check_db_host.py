import re

with open('.env', 'r') as f:
    content = f.read()
    
match = re.search(r'PG_HOST=(.*)', content)
if match:
    print(f"PG_HOST: {match.group(1)}")
else:
    print("PG_HOST not found")
