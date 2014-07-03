# Open Source SAP Calculator
Licence: GNU GPL Affero

## SAP
SAP is the UK Government’s Standard Assessment Procedure for Energy Rating of Dwellings produced by BRE the Building Research Establishment. The worksheet here is based on the 2012 draft version of the SAP worksheet: 
http://www.bre.co.uk/filelibrary/SAP/2012/Draft_SAP_2012_December_2011.pdf
http://www.bre.co.uk/sap2012/page.jsp?id=2759

SAP is one of the key tools for modelling the performance of buildings and then being able to investigate the effects of building fabric improvements such as better insulation and draught proofing. 

## Motivations
The SAP procedure and worksheet is described openly in the SAP specification pdf however to date there appears to be no open source web based implementations of the worksheet.

This is an effort to create such a worksheet so that we can do these calculations on our own homes, understand the calculations and be able to get the data in and out in the way that we may find most useful.

An online and open source form could also make the SAP worksheet more accessible to a wider audience, it would be easier to start doing the SAP calculations out of a general interest and we hope it will help motivate people to start considering the building fabric energy efficiency improvements that can have such a large impact in terms of reducing heating energy requirements and so building a sustainable world. 

## Emoncms integrations
By implementing the worksheet as a module within emoncms we could potentially link parts of the spreadsheet to real monitoring data collected within emoncms and to other tools such as the energy stack module which gives an energy overview based on David MacKay's book sustainable energy without the hot air. Together these tools form a comprehensive home energy toolkit.

# Development

The initial plan is to create the worksheet in a rough and just functional form, nothing fancy to start with and to fill the form close reference to the SAP specification document will be needed in order to understand what the inputs are and to get the table data etc. In future version we can imagine there being options to automatically bring in the table data depending on your location, ability to minimize/maximise sections, miss out and hide sections that are not applicable and so on.

If you would like to get involved with developing this, please get in contact on the OpenEnergyMonitor forums.

Thanks!
