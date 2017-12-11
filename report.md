# CO657 Assessment 3 - Cooksey Farm
### H. Le Nguyen, D. Aygun, D. Bard

---

## Our Scenario

In the day to day operations of the farm, Farmer Cooksey needs to have an easy way to have an overview of all the operations and the status' of his crops.  
Our scenario takes into account the fact that he doesn't want to spend his time checking graphs and so will want to only check a simple dashboard once a day in order to help plan his day. He would want to utilise his sensors to notify him when each area of his crops is in need of attention or could do in the near future.

## How does our solution solve the scenario?

Our solution focuses on the single view aspect of the scenario where Farmer Cooksey wishes to only check once or twice a day to see the current conditions of the devices and the conditions over the past 12 hours.  
To facilitate this, the main feature of our site is the notification system which will add a notification to the list whenever any of the systems go out of the specified range. These ranges are informed from both the project brief, looking at the already logged data and using the provided data links and further research to get appropriate values for the different crops in the greenhouses and outside <a href="#ref1"><sup>i</sup></a>. These notifications appear when a large enough percentage of the readings within the last 12 hours are outside the optimum data range which implies that there is more than just a temporary glitch in the system caused by a networking error or an alternate bug.  
If the farmer wishes to then see if these values are still outside the range, the user can then switch to the "Most Recent" tag which will display the sensors which are currently outside their optimum values. This will help inform the farmer as to whether there is still an issue or if the issue has resolved itself. In the case of overnight errors, this will need to be confirmed by looking at the device graphs.  
In addition to this, the "Device Status" pane displays the current operational status of each device connected to the system. If there has been consistent non-responses from any of the devices, the location name will turn red and to let the farmer know straight away and the individual device will also turn red so the farmer knows the specifics.

Another element to allow the farmer to correctly plan his day is the weather pane which displays both the generic overview for the day, as well as the rainfall for the past day. This last element is important since it allows for the farmer to adjust his plans involving the outdoor elements on his farm as well as whether he might need to wear boots as opposed to plain shoes.  
The option also exists for the farmer to quickly access the MET office 5-day forecast which can give much further details as to the weather conditions for the next few days, allowing for further planning.

If the farmer wishes for a more detailed overview of the sensor data, the site map allows for the farmer to navigate to and view the most recently received piece of data. It utilises the Google Maps data of the farm to allow the farmer to quickly navigate between locations so he doesn't need to remember the exact name given to each location on his farm. Clicking on a location will load a list of all the sensors which have been registered to that location with appropriate naming and data to make it easy for the farmer to understand.

As a final element on the dashboard page, there is a selectable graphical interface which displays the last 12 hours of data for any of the sensors available on the farm. This is intended to be used if the farmer wishes to know how his farm has fared overnight or if during the day if the above alerts have informed him if there is an issue.

## Reasoning for Implementation

The main reason we wanted to use a single page layout for the main features was under the rational that the farmer wouldn't want to be constantly checking up on the different sensors or having to trawl through graphs in order to figure out what needed doing on his daily rounds.  
The layout also puts an emphasis on the notifications which is akin to the list of tasks which the farmer specifically requests within the specs provided. However, if the farmer did have an additional few hours, then the individual sensor graphs at the bottom of the dashboard allow the user to then see more detailed information and draw his own conclusions.

The entire system has been built as a web interface on top of the Laravel web framework with the idea of it being run on an external server. This allows for an increase in access speed due to the majority of the processing being done server-side as well as an additional security benefit of offsite data storage. This doesn have the issue of a large set-up time, however this is a one off expense and any further expansion to the software can be done without any significant downtime.

The client-side application was built using the bootstrap framework which allows for the display to easily adapt to any screen size allowing the farmer to easily access the dashboard from either at a desktop or on his phone if he's away.  
As part of the UI design, we have laid the features out to ensure that the most important ones are at the top, but the rest can be accessed easily from the main page.

For the weather data, we wanted to utilise the real-world data coming in from governmental <a href="#ref2"><sup>ii</sup></a> and MET office <a href="ref3"><sup>iii</sup></a> sources which allow for both accuracy and speed of access. This does need to be monitered in terms of whether either source changes their api, however this is unlikely due to the scale of the sources.

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