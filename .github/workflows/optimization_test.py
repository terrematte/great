import subprocess
import json
import time
import matplotlib.pyplot as plt

def call_shell_script():
    start_time = time.time()
    subprocess.run(['bash', 'example_requests/default_request.sh'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    end_time = time.time()

    try:
        with open("output.json", "r") as file:
            data = json.load(file)
            return data.get('time', end_time - start_time)  # Fallback to measured time if 'time' is missing
    except (json.JSONDecodeError, FileNotFoundError):
        print("Error: Could not read output.json")
        return None

times = []
num_calls = 10

for _ in range(num_calls):
    time_taken = call_shell_script()
    if time_taken is not None:
        print(f"Time taken: {time_taken} seconds")
        times.append(time_taken)
    else:
        print("Error: No valid time data")
        times.append(0)

plt.figure(figsize=(10, 6))
plt.plot(range(1, num_calls + 1), times, marker='o', linestyle='-', color='b')
plt.title('Time Taken by default_request.sh')
plt.xlabel('Call Number')
plt.ylabel('Time (seconds)')
plt.grid(True)

plt.savefig(r".github/workflows/data/time_usage.png")

print("Plot saved as time_usage.png")
