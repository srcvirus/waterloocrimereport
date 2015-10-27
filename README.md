The PHP implementation was done in a 64-bit Ubuntu 13.10 environment. This README file describes the steps to install the dependencies and necessary configurations to get the PHP implementation running. After copying the php directory and setting up all dependencies, going to `http://localhost` should display the web page that was shown as a demo.

Dependencies
------------

### Web server (Recommended: Apache)
Any web server that can run PHP code as server side scripts will work (e.g., Apache, Lighthttp, ngnix). The development was done using Apache 2 webserver and other web servers have not been tested. Therefore, it is recommended to install Apache. After installing apache its **rewrite** module needs to be enabled. The **rewrite** module rewrites the RESTful URIs to prepend index.html in front of them.

```
sudo apt-get install apache2
sudo a2enmod rewrite
```

A sample apache configuration file (**datamagic-apache.conf**) is provided in this git repository. Copy this configuration file to apache's sites available directory (**/etc/apache2/sites-available**) and enable the site. One thing to note here is change the DocumentRoot directive in the configuration file to the top-most directory of PHP implementation. Also make sure that all the directories in that path has read permission available. Otherwise accessing the site might return **403 Forbidden** status code. Also, disable apache's default site. After these steps restart apache to take these changes effect.

```
sudo cp datamagic-apache.conf /etc/apache2/sites-available/datamagic.conf
sudo a2dissite 000-default
sudo a2ensite datamagic.conf
sudo service apache2 restart
```

### PHP 5.3 or above
Install PHP 5.3 or above for correct functionality of the implementation. The PHP implementation also depends on some other PHP libraries (json, mongo, curl). In order to install these libraries install the PHP Extension and Application Repository manager (PEAR) package. Run the following commands to install PHP and related libraries.

```
sudo apt-get install php5 php5-pear php5-dev php5-cli
sudo apt-get install php5-json php5-curl
sudo pecl install mongo
```
Edit the `/etc/php5/apache2/php.ini` file and add the line `extension=mongo.so` to enable mongodb driver for PHP. After making these changes restart apache.

### Slim PHP framework
Slim is provided with the distribution. No installation is necessary. The RESTful URIs need to be rewritten to prepend index.php at the beginning to indicate that these are handled by index.php file. The rewrite rules are written in a `.htaccess` file located at the document root. The requried `.htaccess` file is provided in the repository. No action is requried here.

### MongoDB

Running the following set of commands in the terminal will install MongoDB in the machine.
```
sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
sudo echo 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen' | sudo tee /etc/apt/sources.list.d/mongodb.list
sudo apt-get update
sudo apt-get install mongodb-10gen
```
These set of commands are summerized from a more elaborate tutorial available [here](http://docs.mongodb.org/manual/tutorial/install-mongodb-on-ubuntu/). 

We have exported our database at some point in time and provided that in the git repository (**data.json**) of PHP implementation. Once mongodb is installed the data can be imported into the system by running the following command:

```
mongoimport -d datamagic -c daily_call_summary --file data.json
```

RESTful API Documentation
-------------------------
The API documentation uses ``localhost`` as the server name. ``localhost`` can be replaced with the server's fully qualified domain name or public IP address for accessing from remote machine. One thing to note here is, the names enclosed withing angular braces are placeholder for parameter values, and the parts enclosed in square braces are optional parameters. Currently the following list of API calls are supported:

##### `http://localhost/crawl/<incident-type>`
This call will crawl the type of page specified by `incident-type` from `www.wrps.on.ca` and will place it in the `data/preprocessed/<incident-type>` directory. One limitation at this point is, this directory needs to have global write permission. This bug has not been resolved yet. Valid values for `incident-type` are: `daily-call-summary` and `major-incidents`.
Example: `http://localhost/crawl/daily-call-summary`

The call will return a JSON object indicating the status:
```
{
  status: "fail" or "success",
  fail_reason: [only present if status is "fail"]
}
```
##### `http://localhost/parse/<incident-type>`
This call will parse all the pages currently in the `data/preprocessed/<incident-type>` directory, will write the data to mongodb, and finally will move the pages to `data/processed/<incident-type>` directory. This directory also needs to have global write permission at this point. Valid values for `incident-type` are: `daily-call-summary` and `major-incidents`
Example: `http://localhost/parse/daily-call-summary`

The call will return a JSON object with same format as the ``crawl`` API call.

##### `http://localhost//incidents/from/<from_time>[/to/<to_time>]`
This call returns a JSON array of objects containing all the incidents that have taken place since ``from_time`` untill ``to_time``. The ``to_time`` parameter is optional, if unspecified defaults to the current date. The time is specified using ISO date strings, with the spaces replaced with '+' symbol. 
Example: `http://localhost/incidents/from/Thu+Apr+03+2014+17:58:37+GMT-0400+(EDT)`

The returned JSON has the following format:

```
[
  {
    _id: id of incident 1,
    title: title of the incident,
    date: {
      sec: unix timestamp,
      usec: 0
    },
    intersection: nearest street intersection of the incident,
    lat: approximate latitude,
    lon: approximate longitude
  }
  { ... },
  { ... },
  .
  .
  .
]
```

##### `http://localhost/incidents/summary/monthly/<month-year>`
This call retunrs a JSON array of objects containing the count of different incident types for the month specified by `month-year`. `month-year` is a `+` separated string containing the name of the month (either in full or short form) and the year. 
Example: `http://localhost/incidents/summary/monthly/Apr+2014`

The returned JSON has the following format:
```
[
  {
    _id: {
      type: name of a category
    },
    count: number of incidents of the type
  },
  { ... },
  { ... },
  .
  .
  .
]
```
##### `http://localhost/incidents/summary[/type/:incident_type][/from/:from_time][/to/:to_time]`
This call returns a JSON array of objects containing aggregate data of different incident types. The optional parameters can be used to specify an incident type (`incident_type`), a start time (`from_time`) and an end time (`to_time`). If these parameters are not speficied aggregate information about all the events from the beginning of time to the current time are returned. The parameter values are '+' delimeted strings and the time parameters are formatted according to ISO date string.
Example: `http://localhost/incidents/summary/type/TRAFFIC+-+OTHER`

The returned JSON has the following format:

```
[
  {
    _id: {
      type: name of a category
    },
    count: number of incidents of this type,
    dates: 
    [
      {
        sec: unix time stamp of incident 1 of this type,
        usec: fractional microsecond value
      },
      { ... }, ...
    ],
    locations: 
    [
      {
        lat: latitude of incident 1 of this type,
        lon: longitude of incident 1 of this type
      },
      { ... }
      .
      .
      .
    ]
  }
  { ... },
  { ... },
  .
  .
  .
]
```
##### `http://localhost/incidents/type`
This call returns a JSON array containing the types of incidents that are currently present in the database. This call does not take any parameter.
Example: `http://localhost/incidents/type`

The returned JSON is formatted as follows:

```
[
  {
    _id: {
      type: name of the category 1
    }
  },
  { ... },
  { ... }.
  .
  .
]
```

The following is a list of types that are currently available in the database:
(the list has been obtained by calling http://162.243.172.62/incidents/types)

* TECHNOLOGY/INTERNET CRIME
* ROBBERY
* PROSTITUTION
* DANGEROUS CONDITION
* BREAK & ENTER
* DISTURBANCE
* FIRE
* OFFENSIVE WEAPON
* ASSAULT
* THEFT OVER $5000
* TRAFFIC - OTHER
* SELECTIVE TRAFFIC ENFORCEMENT PROGRAM (STEP)
* PROPERTY DAMAGE
* THEFT UNDER $5000
* MVC PERSONAL INJURY
* MVC PROP. DAMAGE

