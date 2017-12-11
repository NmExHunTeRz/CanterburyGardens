# CO657 Assessment 3 - Cooksey Farm
### H. Le Nguyen, D. Aygun, D. Bard

---

## Our Scenario

In the day-to-day operations of the farm, Farmer Cooksey needs to be able to easily view the status of his crops and sensors, and he needs an easy way to use sensor data for help with day-to-day planning.
Our scenario takes into account that Farmer Cooksey doesn't want to spend much of his time checking graphs, but rather to have a unified dashboard with only the data that would be useful for him to decide how to plan his day. He would want to utilise his sensors to notify him when each area of his crops is in need of attention or could do in the near future, as well as if each device is still operational, or require attention.

## How does our solution solve the scenario?

Our solution focuses on the single view aspect of the scenario where Farmer Cooksey wishes to only check his data once or twice a day to see the current conditions of the devices and the conditions of his plants and locations over the past 12 hours.  
To facilitate this, the main feature of our site is the notification system which will add a notification to the list whenever any of the systems go out of the specified range. These 'optimal ranges' are informed from both the project brief, looking at the already logged data and using the provided data links and further research to get appropriate values for the different crops in the greenhouses and outside <a href="#ref1"><sup>i</sup></a>. These notifications appear when a percentage of the readings within the last 12 hours are outside the optimum data range which implies that there is more than just a temporary glitch in the system caused by a networking error or an alternate bug.  
If the farmer wishes to then see if these values are still outside the optimal range, the user can then switch to the "Most Recent" tag which will display the sensors which are currently outside their optimum values. This will help inform the farmer as to whether there is still an issue or if the issue has resolved itself. In the case of overnight errors, this will need to be confirmed by looking at the device graphs.  
In addition to this, the "Device Status" pane displays the current operational status of each device connected to the system. If there has been consistent non-responses from any of the devices, the location name will turn red and to let the farmer know straight away and the individual device will also turn red so the farmer knows the specifics. Another element to allow the farmer to correctly plan his day is the weather pane which displays both the generic overview for the day, as well as the rainfall for the past day. This last element is important since it allows for the farmer to adjust his plans involving the outdoor elements on his farm as well as whether he might need to wear boots as opposed to plain shoes.  
The option also exists for the farmer to quickly access the MET office 5-day forecast which can give much further details as to the weather conditions for the next few days, allowing for further planning.

If the farmer wishes for a more detailed overview of the sensor data, the site map allows for the farmer to navigate to and view the most recently received piece of data. It utilises the Google Maps data of the farm to allow the farmer to quickly navigate between locations so he doesn't need to remember the exact name given to each location on his farm. Clicking on a location will load a list of all the sensors which have been registered to that location with appropriate naming and data to make it easy for the farmer to understand. As a final element on the dashboard page, there is a selectable graphical interface which displays the last 12 hours of data for any of the sensors available on the farm. This is intended to be used if the farmer wishes to know how his farm has fared overnight or if during the day if the above alerts have informed him if there is an issue.

## Implementation Details

The product was built as a web interface on top of the Laravel web framework, all hosted locally on XAMPP. In production, this could be hosted on an external server for easier handling of traffic load. The majority of the data processing is done server-side (in /app/Http/Controllers/MainController.php). Upon user request, the server contacts the provided REST API for data, aggregate them into two data collections (called $sites and $devices), performs data processing on each Device object and performs data fusion to create the notifications system on the dashboard. Each Device object holds a raw data array and a processed data array. The raw data is used to generate some of the notifications, and the processed array is mainly used by the front end for visualization.

In terms of data processing, each Device object (in /app/Device.php) attempts to remove null values (if the entire device is null, the processed data will be filled with 0's to maintain graphing purposes), then removes invalid data (data outside the range that each device type may return. These values were hard-coded based on the sensor specs on the Internet of Things Moodle page and discussion panel). The rest of the data processing (data smoothing) was done on the front end, as only the graphs visualisation required them (in /resources/views/graph.blade/php). This processing involves performing a moving average of the data before plotting to the individual graphs. Data processing was also done in the MainController (in /app/Http/Controllers/MainController.php) to pull in weather data from governmental <a href="#ref2"><sup>ii</sup></a> and MET office <a href="ref3"><sup>iii</sup></a> sources. Sensor data in the Dashboard page uses 'minute' period data, but only the last 12 hours of data was kept as our scenario focuses on this page being short-term historical data aiding decision making during the day (especially early morning after waking up).

Data fusion was performed both on the backend and frontend to obtain more useful data representations. An example of this is the notifications system, at the front and centre of the Dashboard page, and with the calculations done in the MainController:processNotifications(). This method iterates through all the available data of each device, notes down each measurement that are outside of the 'optimal' range defined in the Assessment brief (these 'optimal' values are stored in a local MySQL database and the definition can be seen in /database/seeds/ConditionTableSeeder.php), and outputs a percentage value of how many readings were outside optimal range for the past 12 hours. Additionally, the method outputs an array of the most recent reading that were outside optimal range. Additionally, data fusion was performed on the 'Device Statuses' panel. Each Device object holds an attribute $notify which would be set to true if the last-connected time was older than 12 hours ago (hence device connection or power may need checking), or if the device has returned null values for the past 20 readings (hence the sensor on the device may be broken/disconnected).

In terms of robustness, we are handling null values (from one null value to data array filled with null values) in data processing, devices which may be disconnected/broken are being reported to Farmer Cooksey, and there is a 'Preferences' page so Farmer Cooksey can change his range of optimal measurements as the seasons change and the crops change.

The client-side application was built using the bootstrap framework which allows for the display to easily adapt to any screen size allowing the farmer to easily access the dashboard from either at a desktop or on his phone if he's away.  As part of the UI design, we have laid the features out to ensure that the most important ones are at the top, but the rest can be accessed easily from the main page. The different visualisation modes are the fused and processed data above in the notifications, device statuses, graphs, and weather data. In addition, a map was provided which displays the most recent (minute) reading of the data for each site.

## Additional Features

Beyond the scenario, there are a few additional features which enhance the user experience.

- Full Data Graphs
    - In the "Graphs" tab of the navigation bar, there is the option to view more detailed graphs for each one of the locations available on the farm. This data is broken down into broad areas of data so that the farmer doesn't have to worry about trying to remember where each individual sensor is on the farm.  
    The data over the past 7 days allows the user to get a broader idea as to the state of the farm in the different areas as well as easily see any trends with regards to day/night cycles.  
    This goes beyond the quick overview of the scenario and is more designed for when the user is doing longer term planning.
- Login Functionality
    - In order to allow for security of the site and its' data, login functionality has been implemented. This only has single user access (Farmer Cooksey) since only the farmer would want to read the data, but it could be extended in the future to allow farm-hands etc. to access it.  
    This is important to ensure the security of the data which includes location data and other sensitive information.
- Condition Preferences
    - In order to account for the rotation of crops and allow for experimentation on the part of the user, the ability for the user to set the optimal conditions for each location on the site has been implemented. This allows for longer term planning on the part of the user as well as being able to easily adjust with differing conditions.

## Future Developments

- Extensibility
    - The main development which would be added to develop this software further would be to add extensibility for the case that the farmer wishes to add more locations to his sensor grid or that the farmer wanted to expand his farming empire. The use of different graphing data would also allow for him to make comparisons between his locations in order to optimise the yield he can get from each crop.
- Historical Tracking
    - In order to allow for even further planning and operational testing, the software could be expanded to add longer term data analysis and graphing options. This would allow for the farmer to compare his crop yields from year to year and see what sorts of conditions he could use to maximise profit whilst minimising effort.
- Power Tracking
    - One thing that Farmer Cooksey was concerned about in his video brief was the power consumption of his farm and how much it could be offset by the solar system. An extension to the current system would be to add a power tracking element which would both show how much power is being used by each sensor and generated by his solar array, but also how much money it is saving him and he is making by selling the excess back to the grid. This would allow him to keep better track of his finances as well as seeing if it is viable for him to expand that area of his operations.

---

<sup id="ref1">i</sup> References used for the planting research

- https://home.howstuffworks.com/cactus-care3.htm
- https://www.rhs.org.uk/advice/profile?PID=849
- https://www.almanac.com/plant/lettuce
- https://extension.psu.edu/seed-and-seedling-biology
- https://www.extension.umn.edu/garden/yard-garden/vegetables/growing-carrots-and-root-vegetables/
- https://aggie-horticulture.tamu.edu/archives/parsons/trees/cactus.html

<sup id="ref2">ii</sup> Rainfall data gained from Environment Agency API
- https://environment.data.gov.uk/flood-monitoring/doc/rainfall

<sup id="ref3">iii</sup> Weather data gained from the MET office API
- https://www.metoffice.gov.uk/datapoint