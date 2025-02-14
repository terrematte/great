import subprocess
import json
import matplotlib.pyplot as plt

def call_shell_script():
    result = subprocess.run(['bash', 'example_requests/default_request.sh'], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    output = result.stdout.decode('utf-8')
    try:
        print(output)
        data = json.loads(output)
        return data.get('time', None)
    except json.JSONDecodeError:
        print("Error decoding JSON")
        return None

times = []
num_calls = 100

for _ in range(num_calls):
    time = call_shell_script()
    if time is not None:
        times.append(time)
    else:
        times.append(0)

plt.figure(figsize=(10, 6))
plt.plot(range(1, num_calls + 1), times, marker='o', linestyle='-', color='b')
plt.title('Time Taken by default_request.sh')
plt.xlabel('Call Number')
plt.ylabel('Time (seconds)')
plt.grid(True)
plt.show()
