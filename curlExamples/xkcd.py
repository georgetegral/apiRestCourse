import urllib.request
import json

def main():
        url = 'https://xkcd.com/info.0.json'
        response = urllib.request.urlopen(url)
        data = json.loads(response.read())
        print (json.dumps(data, sort_keys=True))

if __name__ == "__main__":    main()