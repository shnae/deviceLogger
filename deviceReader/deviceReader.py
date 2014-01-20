class Device:
	def __init__(self, deviceID, deviceName, deviceDescription, hostName, deviceMake, deviceModel, deviceUri):
		self.id = deviceID
		self.name = deviceName
		self.description = deviceDescription
		self.hostname = hostName
		self.make = deviceMake
		self.model = deviceModel
		self.uri = deviceUri
		self.metrics = list()
		self.xmlDoc = ""
	def xmlPath(self): #returns the uri where device's xml page is stored
		return "http://" + self.hostname + "/" + self.uri
	def getXml(self): #sets var xml as the returned xml from the device, also returns the xml
		response = urllib2.urlopen(self.xmlPath())
		
		#self.xmlDoc = response.read()
		#we need to strip namespaces in order for later code to work, strip them if found...
		unmodifiedXml = response.read()
		regEx = re.compile("xmlns\=+.*>")
		self.xmlDoc = re.sub(regEx, ">",unmodifiedXml)
		#print(self.xmlDoc)
		return self.xmlDoc
	#def xmlDoc(self):
		#return self.xml
def getSettings(): #reads settings for db connection etc from a text file called 'settings.txt'
	print("getting settings from disk")
	settingsFile = open("settings.txt")
	dbServerHostname = settingsFile.readline().rstrip('\r\n')
	dbServerUser = settingsFile.readline().rstrip('\r\n')
	dbServerPassword = settingsFile.readline().rstrip('\r\n')
	dbName = settingsFile.readline().rstrip('\r\n')
	settingsValues = dict(dbServerHostname = dbServerHostname, dbServerUser = dbServerUser, dbServerPassword = dbServerPassword, dbName = dbName)
	return settingsValues

	
	
import time
import datetime
import re 	
import os
import MySQLdb
import xml.etree.ElementTree as ET
import urllib2
import StringIO
settingsValues = getSettings()
print('got values...')


print('DB Server: ' + settingsValues['dbServerHostname'])
print('DB User: ' + settingsValues['dbServerUser'])
print('DB Password: ' + settingsValues['dbServerPassword'])
print('DB Name: ' + settingsValues['dbName'])

while 1:
#connect to the database...(this may be pulled later, just shows the db version at the console)...
	print('*************now connecting to database...****************')
	try:
		connection = MySQLdb.connect(settingsValues['dbServerHostname'], settingsValues['dbServerUser'], settingsValues['dbServerPassword'], settingsValues['dbName'])
		connection.query("SELECT VERSION()")
		result = connection.use_result()
		print "MySQL version: %s" % result.fetch_row()[0]
		connection.close()
	except MySQLdb.Error, e:
		print("error, can't connect to the database. The error was: " + str(e))
	finally:
		print(" ")
		
	#retrieve all Settings stored in the database...
	print('***************Getting settings from the Database...*********************')
	try:
		connection = MySQLdb.connect(settingsValues['dbServerHostname'], settingsValues['dbServerUser'], settingsValues['dbServerPassword'], settingsValues['dbName'])
		cursor = connection.cursor()
		cursor.execute("SELECT value FROM settings WHERE name = 'devicePingInterval';")
		results = cursor.fetchone()
		devicePingInterval = int(results[0])
		cursor.execute("SELECT value FROM settings WHERE name = 'reInitializeInterval';")
		results = cursor.fetchone()
		reInitializeInterval = int(results[0])
		print("Setting: devicePingInterval = " + str(devicePingInterval))
		print("Setting: devicePingInterval = " + str(reInitializeInterval))
		cursor.close()
		
		connection.close()
	except MySQLdb.Error, e:
		print("error")
	finally:
		print("*******************Device Properties************************")	
		
	#retrieve all Devices and place into Device objects...
	print('***************Enumerating Devices...*********************')
	try:
		connection = MySQLdb.connect(settingsValues['dbServerHostname'], settingsValues['dbServerUser'], settingsValues['dbServerPassword'], settingsValues['dbName'])
		cursor = connection.cursor()
		cursor.execute("SELECT iddevices, Name, Description, HostName, Make, Model, Url FROM devices;")
		results = cursor.fetchall()
		allDevices = list()
		for row in results:
			#print a confirmation that we got a device...
			print("Device found: DeviceID:" + str(row[0]) + "  Name: " + row[1])
			#create a Device object with the information we received...
			thisDevice = Device(row[0],str(row[1]),str(row[2]),str(row[3]),str(row[4]),str(row[5]),str(row[6]))
			allDevices.append(thisDevice)
		cursor.close()
		connection.close()
	except MySQLdb.Error, e:
		print("error")
	finally:
		print("*******************Device Properties************************")	
	#Show all the devices/properties we've gotten and created objects for...
	for device in allDevices:
		print("Device ID: " + str(device.id) + "   Name: " + device.name)
		print("Description: " + device.description)
		print("Make: " + device.make + ", Model: " + device.model)
		print("Hostname: " + device.hostname + " Uri: " + device.uri)
		print(device.xmlPath())
		print("_____________________________________________________________")
	print("**********************Device Metrics*****************************")
	#Now, query the database for metric definitions...
	for device in allDevices:
		try:
			connection = MySQLdb.connect(settingsValues['dbServerHostname'], settingsValues['dbServerUser'], settingsValues['dbServerPassword'], settingsValues['dbName'])
			cursor = connection.cursor()
			cursor.execute("SELECT iddevicemetrics, name, xmlTag, device FROM devicemetrics WHERE device =" + str(device.id))
			results = cursor.fetchall()
			#allMetrics = list()
			for row in results:
				#print a confirmation that we got a metric...
				print("Metric found: DeviceID: " + str(row[3]) + "  Metric ID: " + str(row[0]) + " Metric Name: " + row[1] + " Xml Tag: " + row[2])
				#add to the metrics list values contained in each device object....
				metricsList = dict(deviceID=row[3], deviceMetric=row[0], deviceName=row[1], deviceXPath=row[2],  datapoint="",  timestamp="")
				device.metrics.append(metricsList)
			cursor.close()
			connection.close()
			#device.xmlDoc = device.getXml()
			#print(device.xmlDoc)
		except MySQLdb.Error, e:
			print("error retrieving metrics")
	print("Done retrieving Devices and Metrics.........")
	print("")
	print("")

	print("********************Datapoints:************************************")
	#Now, we will start putting the latest datapoints into the device objects and populating the database...		
	timesThroughLoop = 1
	while (timesThroughLoop < reInitializeInterval): #run through this loop until we have met the reInitializeInterval counter, then restart.
		for device in allDevices:
			try: 
				device.xmlDoc = device.getXml()
				from xml.etree.ElementTree import XML, fromstring, tostring
				metricIndex = 0
				#print("XML####################################################")
				#print(device.xmlDoc)
				#print("XML@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@")
				print("DevicePinging device " + device.name + ".............")
				elem = XML(device.xmlDoc)
				for metric in device.metrics:
					xPathSearchString =   device.metrics[metricIndex]["deviceXPath"]
					thisDatapoint = elem.find(xPathSearchString).text
					#print(thisDatapoint)
					device.metrics[metricIndex]["datapoint"] = thisDatapoint
					print("Retrieved datapoint: " + str(device.metrics[metricIndex]["deviceMetric"])  + " " + str(thisDatapoint))
					#test to see if we got a datapoint, if so, put it in the db...
					if(device.metrics[metricIndex]["deviceMetric"] != ""):
						try:
							connection = MySQLdb.connect(settingsValues['dbServerHostname'], settingsValues['dbServerUser'], settingsValues['dbServerPassword'], settingsValues['dbName'])
							cursor = connection.cursor()
							timestamp = datetime.datetime.now()
							#timestamp.strftime('%Y-%m-%d %H:%M-%S')
							mySqlTimestamp = timestamp.strftime('%Y-%m-%d %H:%M-%S')
							#sqlCommand = "INSERT INTO datapoints (device, metric, datapoint, timestamp) VALUES (%s,%s,%s,%s);" , str(device.id), str(device.metrics[metricIndex]["deviceMetric"]),str(device.metrics[metricIndex]["datapoint"]), str(timestamp)
							#cursor.execute(sqlCommand)
							#sqlCommand = "INSERT INTO datapoints (device, metric, datapoint, timestamp) VALUES (" + str(device.id) + "," +  str(device.metrics[metricIndex]["deviceMetric"]) + "," +  str(device.metrics[metricIndex]["datapoint"]) + ",'" +  mySqlTimestamp + "')"
							#print(sqlCommand)
							cursor.execute("INSERT INTO datapoints (device, metric, datapoint, timestamp) VALUES (" + str(device.id) + "," +  str(device.metrics[metricIndex]["deviceMetric"])
							+ "," +  str(device.metrics[metricIndex]["datapoint"]) + ", '" +  mySqlTimestamp + "')")
							#,, str(timestamp)))
							
							cursor.close()
							connection.commit()
							connection.close()
							print("successfully wrote datapoint.")
							
						except MySQLdb.Error, e:
							print("Error placing datapoint in database, the error was: " + str(e))
							
						except Exception, err:
							print("some error occured, here it is: " + str(err))
							
						
							
					#print(device.metrics[metricIndex]["datapoint"])
					metricIndex += 1
			except Exception, err:
				print("problem working with retreiving datapoint for Device" + device.name)
				print("the xpath expression was: " + xPathSearchString)
				print("the error was " + str(err))
				

	#Now, enter the retrieved datapoints into the database:

		
		time.sleep(devicePingInterval)
		timesThroughLoop += 1
	timesThroughLoop = 0
