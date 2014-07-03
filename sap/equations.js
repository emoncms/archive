function calculate(data) { 


var region = data['region'];
data['H5a'] = region;
if (region ==0) region =1;

// Table 1a: Number of days in month, nm
data['table1a'] = [31,28,31,30,31,30,31,31,30,31,30,31];

// Table 1c: Monthly factors for hot water use
data['table1c'] = [1.1,1.06,1.02,0.98,0.94,0.90,0.90,0.94,0.98,1.02,1.06,1.10];

// Table 1d: Temperature rise of hot water drawn off (∆Tm, in K)
data['table1d'] = [41.2,41.4,40.1,37.6,36.4,33.9,30.4,33.4,33.5,36.3,39.4,39.9];

var table_u1 = [
[4.5,5.0,6.8,8.7,11.7,14.6,16.9,16.9,14.3,10.8,7.0,4.9],
[5.1,5.5,7.4,9.3,12.6,15.4,17.8,17.8,15.1,11.6,7.6,5.5],
[5.3,5.4,7.4,9.2,12.6,15.2,17.6,17.8,15.2,11.9,7.9,5.8],
[5.5,5.7,7.5,9.2,12.6,15.2,17.4,17.6,15.1,11.8,8.1,5.9],
[6.4,6.5,7.9,9.3,12.4,14.7,16.8,17.0,14.9,12.0,8.9,7.0],
[5.4,5.7,7.5,9.2,12.6,15.2,17.4,17.3,14.9,11.5,8.0,5.9],
[4.9,5.3,7.1,9.0,12.2,14.9,17.2,17.1,14.5,11.0,7.3,5.2],
[5.1,5.5,7.0,8.9,12.0,14.5,16.6,16.5,14.1,10.9,7.6,5.4],
[4.6,4.9,6.2,8.1,11.1,13.5,15.5,15.4,13.1,10.1,7.0,4.9],
[4.3,4.7,6.1,7.9,10.7,13.4,15.5,15.4,13.0,9.8,6.6,4.6],
[4.5,4.9,6.4,8.2,11.2,14.0,16.2,16.1,13.6,10.3,6.9,4.8],
[4.5,5.0,6.8,8.7,11.7,14.6,16.9,16.9,14.3,10.8,7.0,4.9],
[4.6,5.0,7.0,9.0,12.2,15.0,17.5,17.6,15.0,11.4,7.3,5.1],
[5.7,5.8,7.3,8.9,12.0,14.3,16.4,16.3,14.2,11.2,8.2,6.2],
[4.8,4.9,6.0,7.9,10.8,13.1,14.9,14.8,12.8,9.8,7.0,5.1],
[4.1,4.4,5.7,7.7,10.6,13.2,15.2,15.0,12.7,9.4,6.3,4.3],
[4.0,4.2,5.5,7.4,10.1,12.8,14.9,14.7,12.5,9.2,6.2,4.1],
[4.4,4.3,5.5,7.4,10.2,12.5,14.5,14.4,12.3,9.2,6.5,4.5],
[5.3,5.0,5.9,7.4,9.8,11.7,13.7,13.7,12.1,9.5,7.2,5.6],
[4.9,4.5,5.2,6.9,9.1,11.2,13.3,13.6,11.9,9.3,6.9,5.2],
[4.7,4.1,4.7,6.4,8.5,10.6,12.7,13.0,11.4,8.8,6.5,4.9],
[5.2,5.4,6.8,8.3,11.1,13.4,15.4,15.2,13.2,10.2,7.4,5.5]];

// Table U2: Wind speed (m/s) for calculation of infiltration rate
var table_u2 = [
[5.4,5.1,5.1,4.5,4.1,3.9,3.7,3.7,4.2,4.5,4.8,5.1],
[4.8,4.6,4.5,4.0,3.7,3.5,3.3,3.3,3.7,4.0,4.3,4.6],
[4.2,4.0,4.0,3.5,3.2,3.0,2.9,2.9,3.3,3.5,3.7,4.0],
[4.2,4.0,4.0,3.5,3.2,3.0,2.9,2.9,3.3,3.5,3.7,4.0],
[7.1,6.8,6.8,5.9,5.5,5.2,5.0,5.0,5.6,6.1,6.4,6.8],
[4.8,4.6,4.5,4.0,3.7,3.5,3.3,3.3,3.7,4.0,4.3,4.6],
[4.8,4.6,4.5,4.0,3.7,3.5,3.3,3.3,3.7,4.0,4.3,4.6],
[5.4,5.1,5.1,4.5,4.1,3.9,3.7,3.7,4.2,4.5,4.8,5.1],
[6.0,5.7,5.7,5.0,4.6,4.4,4.2,4.2,4.7,5.1,5.4,5.7],
[6.0,5.7,5.7,5.0,4.6,4.4,4.2,4.2,4.7,5.1,5.4,5.7],
[6.0,5.7,5.7,5.0,4.6,4.4,4.2,4.2,4.7,5.1,5.4,5.7],
[5.4,5.1,5.1,4.5,4.1,3.9,3.7,3.7,4.2,4.5,4.8,5.1],
[5.4,5.1,5.1,4.5,4.1,3.9,3.7,3.7,4.2,4.5,4.8,5.1],
[6.5,6.3,6.2,5.4,5.1,4.8,4.6,4.6,5.1,5.6,5.9,6.3],
[6.5,6.3,6.2,5.4,5.1,4.8,4.6,4.6,5.1,5.6,5.9,6.3],
[6.5,6.3,6.2,5.4,5.1,4.8,4.6,4.6,5.1,5.6,5.9,6.3],
[7.1,6.8,6.8,5.9,5.5,5.2,5.0,5.0,5.6,6.1,6.4,6.8],
[7.1,6.8,6.8,5.9,5.5,5.2,5.0,5.0,5.6,6.1,6.4,6.8],
[7.7,7.4,7.3,6.4,6.0,5.7,5.4,5.4,6.0,6.6,7.0,7.4],
[8.3,8.0,7.9,6.9,6.4,6.1,5.8,5.8,6.5,7.1,7.5,8.0],
[8.9,8.6,8.5,7.4,6.9,6.5,6.2,6.2,7.0,7.6,8.0,8.6],
[6.0,5.7,5.7,5.0,4.6,4.4,4.2,4.2,4.7,5.1,5.4,5.7]];

//----------------------------------------------------------------------------------------------------------------
// 1. Overall dwelling dimension
//----------------------------------------------------------------------------------------------------------------

// Volume  = Floor Area * Height
data['3a'] = data['1a'] * data['2a']; // Volume of basement
data['3b'] = data['1b'] * data['2b']; // Volume of ground floor
data['3c'] = data['1c'] * data['2c']; // Volume of first floor
data['3d'] = data['1d'] * data['2d']; // Volume of second floor
data['3e'] = data['1e'] * data['2e']; // Volume of third floor
data['3f'] = data['1f'] * data['2f']; // Volume of other floor 1
data['3g'] = data['1g'] * data['2g']; // Volume of other floor 2
data['3h'] = data['1h'] * data['2h']; // Volume of other floor 3

// Calculate total floor area TFA
data['4'] = data['1a'] + data['1b'] + data['1c'] + data['1d'] + data['1e'] + data['1f'] + data['1g'] + data['1h'];

// Calculate dwelling volume
data['5'] = data['3a'] + data['3b'] + data['3c'] + data['3d'] + data['3e'] + data['3f'] + data['3g'] + data['3h'];

//----------------------------------------------------------------------------------------------------------------
// 2. Ventilation rate
//----------------------------------------------------------------------------------------------------------------

/*
   FACTORS: 
 - Number of chimneys, flues, fans and vents
 - Number of floors
 - Extent of window and door draught proofing
 - Degree of shelter
 - Modified for wind speed = (wind speed / 4) x infiltration rate
 - Modified for type of ventilation system
*/

// The following calculation provides a method to estimate air-changes per hour in a building

data['6ad'] = data['6aa'] + data['6ab'] + data['6ac'];  // total number of chimneys
data['6a'] = data['6ad'] * 40;                          // m3 per hour from chimneys

data['6bd'] = data['6ba'] + data['6bb'] + data['6bc'];  // total number of open flues
data['6b'] = data['6bd'] * 20;                          // m3 per hour from open flues

data['7a'] = data['7aa'] * 10;                          // m3 per hour from intermittent fans
data['7b'] = data['7ba'] * 10;                          // m3 per hour from passive vents
data['7c'] = data['7ca'] * 40;                          // m3 per hour from flueless gas fires

// Calculate total m3 per hour from above
data['8a'] = data['6a'] + data['6b'] + data['7a'] + data['7b'] + data['7c'];

// Caclulate m3 per hour / dwelling volume = air changes per hour from above 
data['8'] = data['8a'] / data['5'];

// Calculate additional infiltration proportional to number of floors
data['10'] = (data['9'] - 1) * 0.1;

// Calculate window infiltration: 
// data['14'] = percentage of windows and doors draught proofed
data['15'] = 0.25 - (0.2 * data['14'] / 100);

// Sum of all infiltration sources:
// data['11'] = Structural infiltration: 0.25 for steel or timber frame or 0.35 for masonry construction
// data['12'] = If suspended wooden floor, enter 0.2 (unsealed) or 0.1 (sealed), else enter 0
// data['13'] = If no draught lobby, enter 0.05, else enter 0
data['16'] = data['8'] + data['10'] + data['11'] + data['12'] + data['13'] + data['15'];


// !!!! NOT FULLY IMPLEMENTED !!!!
// If based on air permeability value, then (18) = [(17) ÷ 20]+(8), otherwise (18) = (16)
// data['17'] = Air permeability value, q50, expressed in cubic metres per hour per square metre of envelope area
// Air permeability value applies if a pressurisation test has been done, or a design or specified air permeability is being used
data['18'] = data['16'] * 1;

// Take into account shelter
// Shelter factor (20) = 1 - [0.075 × (19)]
// data['19'] = Number of sides on which dwelling is sheltered
data['20'] = 1 - (0.075 * data['19']);

// Infiltration rate incorporating shelter factor (18) × (20)
data['21'] = data['18'] * data['20'];

// Infiltration rate modified for monthly wind speed

// Copy in wind speed figures for region from table_u2
for (var i=1; i<13; i++) data['22-'+i] = table_u2[region][i-1];

// Calculate Wind Factor (22a)m = (22)m ÷ 4
for (var i=1; i<13; i++) { data['22a-'+i] = data['22-'+i] / 4; }

// Adjusted infiltration rate (allowing for shelter and wind speed) = (21) × (22a)m
for (var i=1; i<13; i++) { data['22b-'+i] = data['22a-'+i] * data['21']; }


// data['23a'] = If mechanical ventilation: air change rate through system
// data['23b'] = If exhaust air heat pump using Appendix N, (23b) = (23a) × Fmv (equation (N4)) , otherwise (23b) = (23a)
// data['23c'] = If balanced with heat recovery: efficiency in % allowing for in-use factor (from Table 4h)

// a) If balanced mechanical ventilation with heat recovery (MVHR) (24a)m = (22b)m + (23b) × [1 – (23c) ÷ 100]
for (var i=1; i<13; i++) { data['24a-'+i] = data['23c'] / -100 + 1 * data['23b'] + data['22b-'+i]; }

// b) If balanced mechanical ventilation without heat recovery (MV) (24b)m = (22b)m + (23b)
for (var i=1; i<13; i++) { data['24b-'+i] = data['22b-'+i] + data['23b']; }

//c) If whole house extract ventilation or positive input ventilation from outside
//if (22b)m < 0.5 × (23b), then (24c) = (23b); otherwise (24c) = (22b) m + 0.5 × (23b)
for (var i=1; i<13; i++) 
{ 
  if (data['24b-'+i] < (0.5*data['23b'])) 
    data['24c-'+i] = data['23b']; 
  else 
    data['24c-'+i] = data['22b-'+i] + 0.5*data['23b'];
}

// d) If natural ventilation or whole house positive input ventilation from loft
// if (22b)m ≥ 1, then (24d)m = (22b)m otherwise (24d)m = 0.5 + [(22b)m2 × 0.5]
for (var i=1; i<13; i++) 
{ 
  if (data['22b-'+i] >= 1) 
    data['24d-'+i] = data['22b-'+i]; 
  else 
    data['24d-'+i] = 0.5 + (data['22b-'+i]*data['22b-'+i]*0.5);
}

// Figures are copied into (25)m by javascript on section 2 page

//----------------------------------------------------------------------------------------------------------------
// 3. Heat losses and heat loss parameter
//----------------------------------------------------------------------------------------------------------------

// The U-value table calculations happen as part of the heatloss page.

var totalarea = 0, totalheatloss = 0;
data['itemsheatcapacity'] = 0;
for (z in data['heatlossitems']) {
  totalarea += data['heatlossitems'][z].netarea;
  totalheatloss += data['heatlossitems'][z].axu;
  data['itemsheatcapacity'] += data['heatlossitems'][z].axk;
}
data['31'] = totalarea;
data['externalheatloss'] = totalheatloss;


data['32'] = data['32-1'] * data['32-2'];	// Party wall - loss calc
data['32-4'] = data['32-3'] * data['32-1'];	// Party wall - therm mass calc
data['32a-3'] = data['32a-2'] * data['32a-1'];	// Party floor
data['32b-3'] = data['32b-2'] * data['32b-1'];	// Party ceiling
data['32c-3'] = data['32c-2'] * data['32c-1'];  // Internal wall **
data['32d-3'] = data['32d-2'] * data['32d-1'];	// Internal floor
data['32e-3'] = data['32e-2'] * data['32e-1'];	// Internal ceiling

data['33'] = data['externalheatloss'] + data['32'];

// Heat capacity
data['34'] = data['itemsheatcapacity'] + data['32-4'] + data['32a-3'] + data['32b-3'] + data['32c-3'] + data['32d-3'] + data['32e-3'];

// Thermal mass parameter
data['35'] = data['34'] / data['4'];

data['37'] = data['33'] + data['36'];

// Ventilation heat loss
for (var i=1; i<13; i++) { data['38-'+i] = 0.33 * data['25-'+i] * data['5']; }

// Total heat loss
for (var i=1; i<13; i++) { data['39-'+i] = data['37'] + data['38-'+i]; }

// Average (39)
data['39'] = 0; 
for (var i=1; i<13; i++) { data['39'] += data['39-'+i]; }
data['39'] = data['39'] / 12;

for (var i=1; i<13; i++) { data['40-'+i] = data['39-'+i] / data['4']; }

// Average (40)
data['40'] = 0;
for (var i=1; i<13; i++) { data['40'] += data['40-'+i]; }
data['40'] = data['40'] / 12;


// Copy days in month from table 1a
for (var i=1; i<13; i++) { data['41-'+i] = data['table1a'][i-1]; }


//----------------------------------------------------------------------------------------------------------------
// Solar Hot Water appendix
//----------------------------------------------------------------------------------------------------------------

data['H3b'] = 0.892 * (data['H3'] + 45 * data['H3a']);

data['H4'] = data['H3b'] / data['H2'];

// test: region wales (index:13), orient south (index: 4), inclination 35 degrees
data['H5'] = annual_solar_rad(data['H5a'],data['H5b'],data['H5c']);

data['H7'] = data['H1'] * data['H2'] * data['H5'] * data['H6'];

data['H8'] = data['H7'] / data['45'];

if (data['H8']>0) data['H9'] = 1 - Math.exp(-1/data['H8']); else data['H9'] = 0;

if (data['H4'] < 20) 
  data['H10'] = 0.97 - 0.0367 * data['H4'] + 0.0006 * data['H4'] * data['H4'];
else 
  data['H10'] = 0.693 - 0.0108 * data['H4'];

// Effective solar volume, Veff
data['H13'] = data['H11'] + 0.3 * (data['H12'] - data['H11']);

// Daily hot water demand, Vd,average, (litres)
data['H14'] = data['43'];

// Volume ratio Veff/Vd,average (H13) ÷ (H14)
data['H15'] = data['H13'] / data['H14'];

// Solar storage volume factor
data['H16'] = 1 + 0.2 * Math.log(data['H15']);
if (data['H16']>1) data['H16'] = 1;

//Annual solar input Qs (kWh)
data['H17'] = data['H7'] * data['H9'] * data['H10']* data['H16'];

// Monthly solar input (kWh)
var sum = 0;
for (var m=0; m<12; m++) sum += solar_rad(data['H5a'],data['H5b'],data['H5c'],m);
var annualAverageSolarIrradiance = sum / 12;

for (var i=1; i<13; i++) {
  data['63-'+i] = -1 * data['H17'] * (solar_rad(data['H5a'],data['H5b'],data['H5c'],i-1) / annualAverageSolarIrradiance) * data['table1a'][i-1] / 365.0;
  data['shw-'+i] = data['H17'] * (solar_rad(data['H5a'],data['H5b'],data['H5c'],i-1) / annualAverageSolarIrradiance) / 365.0;

  data['shwB-'+i] = (data['shw-'+i] * 3600000) / (4185.5 * data['H13']);

  data['shwC-'+i] = (data['H7'] * 1 * data['H10']* data['H16']) * (solar_rad(data['H5a'],data['H5b'],data['H5c'],i-1) / annualAverageSolarIrradiance) / 365.0;

  data['shwD-'+i] = (data['shwC-'+i] * 3600000) / (4185.5 * data['H13']);
  data['shwE-'+i] = data['96-'+i] + data['shwD-'+i];
}


data['shwMAX1'] = 1.6 * (data['H7'] * 1 * data['H10']* data['H16']) * (solar_rad(data['H5a'],data['H5b'],data['H5c'],5) / annualAverageSolarIrradiance) / 365.0;
data['shwMAX2'] = (data['shwMAX1'] * 3600000) / (4185.5 * data['H13']);
data['shwMAX3'] = data['96-6'] + data['shwMAX2'];

//----------------------------------------------------------------------------------------------------------------
// 4. Water heating energy requirement
//----------------------------------------------------------------------------------------------------------------

// Annual average hot water usage in litres per day
data['43'] = (data['42']*25) + 36;

// Hot water usage in litres per day for each month V d,m = factor from Table 1c × (43)
for (var i=1; i<13; i++) { data['44-'+i] = data['table1c'][i-1] * data['43']; }

// Total (44)
data['44'] = 0; for (var i=1; i<13; i++) { data['44'] += data['44-'+i]; }

// Energy content of hot water used - calculated monthly = 4.190 × Vd,m × nm × ∆Tm / 3600 kWh/month (see Tables 1b, 1c, 1d)
for (var i=1; i<13; i++) { data['45-'+i] = 4.190 * data['44-'+i] * data['41-'+i] * data['table1d'][i-1] / 3600; }

// Total (45)
data['45'] = 0; for (var i=1; i<13; i++) { data['45'] += data['45-'+i]; }

// Distribution loss (46)m = 0.15 x (45)m
for (var i=1; i<13; i++) { data['46-'+i] = 0.15 * data['45-'+i]; }

// Energy lost from water storage, kWh/day
data['49'] = data['47'] * data['48'];

// Energy lost from water storage, kWh/day (54) = (50) × (51) × (52) × (53) 
data['54'] = data['50'] * data['51'] * data['52'] * data['53'];

// Water storage loss calculated for each month (56)m = (55) × (41)m
for (var i=1; i<13; i++) { data['56-'+i] = data['55'] * data['41-'+i]; }

// If cylinder contains dedicated solar storage, (57)m = (56)m × [(50) – (H11)] ÷ (50), else (57)m = (56)m where (H11) is from Appendix H

if (data['solarcyl']==true) {
  console.log("57m = deticated solar cyl");
  for (var i=1; i<13; i++) { 
    if (data['50']>0) data['57-'+i] = data['56-'+i] * (data['50'] - data['H11']) / data['50']; else data['57-'+i] = 0;
  }
} else {
  console.log("57m = 56m");
  for (var i=1; i<13; i++) { 
    data['57-'+i] = data['56-'+i];
  }
}

// Good to review this calculation:
// Primary loss on a daily basis
var summer = 14 * ( (0.0091 * data['58a'] + 0.0245 * (1 - data['58a'])) * data['58b'] + 0.0263 );
var winter = 14 * ( (0.0091 * data['58a'] + 0.0245 * (1 - data['58a'])) * data['58c'] + 0.0263 );

//Use summer value for June, July, August and September and winter value for other months.
/*data['59-1'] = data['table1a'][0] * winter;
data['59-2'] = data['table1a'][1] * winter;
data['59-3'] = data['table1a'][2] * winter;
data['59-4'] = data['table1a'][3] * winter;
data['59-5'] = data['table1a'][4] * winter;

data['59-6'] = data['table1a'][5] * summer;
data['59-7'] = data['table1a'][6] * summer;
data['59-8'] = data['table1a'][7] * summer;
data['59-9'] = data['table1a'][8] * summer;

data['59-10'] = data['table1a'][9] * winter;
data['59-11'] = data['table1a'][10] * winter;
data['59-12'] = data['table1a'][11] * winter;*/

for (var i=1; i<13; i++) { data['59-'+i] = (data['58'] / 365.0) * data['41-'+i]; }

for (var i=1; i<13; i++) { data['62-'+i] = 0.85 * data['45-'+i] + data['46-'+i] + data['57-'+i] + data['59-'+i] + data['61-'+i];}

// Enter zero's in solar hot water contribution if solar hot water checkbox is not checked
if (!data['solardhw']) for (var i=1; i<13; i++) { data['63-'+i] = 0; }

for (var i=1; i<13; i++) { data['64-'+i] = data['62-'+i] + data['63-'+i]; }

// Total (64)
data['64'] = 0; for (var i=1; i<13; i++) { data['64'] += data['64-'+i]; }
if (data['64']<0) data['64'] = 0;

// Heat gains from water heating, kWh/month 0.25 × [0.85 × (45)m + (61)m] + 0.8 × [(46)m + (57)m + (59)m ]
for (var i=1; i<13; i++) {
  data['65-'+i] = 0.25 * (0.85 * data['45-'+i] + data['61-'+i]) + 0.8 * (data['46-'+i] + data['57-'+i] + data['59-'+i] );
}

//----------------------------------------------------------------------------------------------------------------
// 5. Internal gains
//----------------------------------------------------------------------------------------------------------------


// Metabolic gains (Table 5), watts
for (var i=1; i<13; i++) {
  data['66-'+i] = 60 * data['42'];
}

// Lighting gains (calculated in Appendix L, equation L9 or L9a), also see Table 5

// Appliances gains (calculated in Appendix L, equation L13 or L13a), also see Table 5

// Cooking gains (calculated in Appendix L, equation L15 or L15a), also see Table 5
for (var i=1; i<13; i++) {
  data['69-'+i] = 35 + 7 * data['42'];
}

// Pumps and fans gains (Table 5a) NOT IMPLEMENTED

// Losses e.g. evaporation (negative values) (Table 5)
for (var i=1; i<13; i++) {
  data['71-'+i] = -40 * data['42'];
}

// Water heating gains (Table 5)
for (var i=1; i<13; i++) {
  data['72-'+i] = 1000 * data['65-'+i] / (data['41-'+i] * 24);
}

// Total internal gains = (66)m + (67)m + (68)m + (69)m + (70)m + (71)m + (72)m
for (var i=1; i<13; i++) { data['73-'+i] = data['66-'+i] + data['67-'+i] + data['68-'+i] + data['69-'+i] + data['70-'+i] + data['71-'+i] + data['72-'+i]; }

//----------------------------------------------------------------------------------------------------------------
// 6. Solar gains
//----------------------------------------------------------------------------------------------------------------

// (83)m = Carried out in compiled/6.html

// Total gains – internal and solar (84)m = (73)m + (83)m , watts
for (var i=1; i<13; i++) { data['84-'+i] = data['73-'+i] + data['83-'+i]; }

//----------------------------------------------------------------------------------------------------------------
// 7. Mean internal temperature (heating season)
//----------------------------------------------------------------------------------------------------------------

// Bring calculation of (96)m forward as its used in section 7.
// Monthly average external temperature from Table U1
for (var i=1; i<13; i++) data['96-'+i] = table_u1[region][i-1]-(0.3*data['altitude']/50);

// See utilisationfactor.js for calculation
// Calculation is described on page 149 of SAP document
// Would be interesting to understand how utilisation factor equation 
// was originally derived.
for (var i=1; i<13; i++) 
{ 
  data['86-'+i] = calc_utilisation_factor(data['35'],data['40-'+i],data['39-'+i],data['85'],data['96-'+i],data['84-'+i]);
}

// Table 9c: Heating requirement
// Living area
// 1. Set Ti to the temperature for the living area during heating periods (Table 9)
// 2. Calculate the utilisation factor (Table 9a)
// 3. Calculate the temperature reduction (Table 9b) for each off period (Table 9), u1 and u2, for weekdays

for (var i=1; i<13; i++) 
{ 
  var Th = data['85']; // 21C;
  var R = 1.0; 
  var Ti = data['85'];

  // (TMP,HLP,H,Ti,Te,G, R,Th,toff)
  var u1a = calc_temperature_reduction(data['35'],data['40-'+i],data['39-'+i],Ti,data['96-'+i],data['84-'+i],R,Th,7);
  var u1b = calc_temperature_reduction(data['35'],data['40-'+i],data['39-'+i],Ti,data['96-'+i],data['84-'+i],R,Th,0);
  var u2 = calc_temperature_reduction(data['35'],data['40-'+i],data['39-'+i],Ti,data['96-'+i],data['84-'+i],R,Th,8);
  //console.log('u1a: '+u1a.toFixed(2)+' u1b: '+u1b.toFixed(2)+' u2: '+u2.toFixed(2));

  var Tweekday = Th - (u1a + u2);
  var Tweekend = Th - (u1b + u2);
  data['87-'+i] = (5*Tweekday + 2*Tweekend) / 7;
}

// rest of dwelling
for (var i=1; i<13; i++) data['88-'+i] = 21 - data['40-'+i] + 0.085 *(data['40-'+i]*data['40-'+i]);

for (var i=1; i<13; i++) 
{ 
  var Ti = data['88-'+i];
  var HLP = data['40-'+i];
  if (HLP>6.0) HLP = 6.0;
  // TMP,HLP,H,Ti,Te,G  
  data['89-'+i] = calc_utilisation_factor(data['35'],HLP,data['39-'+i],Ti,data['96-'+i],data['84-'+i]);
}

for (var i=1; i<13; i++) 
{ 
  var Th = data['88-'+i];
  var R = 1.0; 
  var Ti = data['88-'+i];

  var u1a = calc_temperature_reduction(data['35'],data['40-'+i],data['39-'+i],Ti,data['96-'+i],data['84-'+i],R,Th,7);

  var u1b = calc_temperature_reduction(data['35'],data['40-'+i],data['39-'+i],Ti,data['96-'+i],data['84-'+i],R,Th,0);

  var u2 = calc_temperature_reduction(data['35'],data['40-'+i],data['39-'+i],Ti,data['96-'+i],data['84-'+i],R,Th,8);

  var Tweekday = Th - (u1a + u2);
  var Tweekend = Th - (u1b + u2);
  data['90-'+i] = (5*Tweekday + 2*Tweekend) / 7;
}



data['91'] = data['4b'] / data['4'];

for (var i=1; i<13; i++) 
{ 
  data['92-'+i] = (data['91'] * data['87-'+i]) + (1 - data['91']) * data['90-'+i];
}

//----------------------------------------------------------------------------------------------------------------
// 8. Space heating requirement
//----------------------------------------------------------------------------------------------------------------

// See section 7 above for calc of (96)m external temperature.

for (var i=1; i<13; i++) 
{ 
  data['94-'+i] = calc_utilisation_factor(data['35'],data['40-'+i],data['39-'+i],data['92-'+i],data['96-'+i],data['84-'+i]);
//  if (data['94-'+i]==0) data['94-'+i] = 1;
}

for (var i=1; i<13; i++) { data['95-'+i] = data['94-'+i] * data['84-'+i]; }

for (var i=1; i<13; i++) { data['97-'+i] = data['39-'+i] * (data['92-'+i] - data['96-'+i]); }

// Not sure about this bit

for (var i=1; i<13; i++) {
  if (data['39-'+i] == 0) { data['ymean-'+i] = 0; } else { data['ymean-'+i] = data['84-'+i] / (data['39-'+i] * (data['92-'+i] - data['96-'+i])); }
}

for (var i=1; i<13; i++) {

  var a = i - 1; if (a==0) a = 12;
  var beginning = (data['ymean-'+i] + data['ymean-'+a]) / 2;


  var a = i + 1; if (a==13) a = 1;
  var end = (data['ymean-'+i] + data['ymean-'+a]);

  var y1, y2;

  if (beginning < end) y1 = beginning; else y1 = end;
  if (beginning < end) y2 = end; else y2 = beginning;

  var y = data['ymean-'+i];

  var frm = 0;

  var tau = data['35'] / (3.6 * data['40-'+i]);
  var a = 1.0 + tau / 15.0;
  var ylimit = (1+a) / a;

  if (y2 < ylimit) {
    frm = 1;
  } else if (y1 > ylimit) { 
    frm = 0;
  } else {
    if (y > ylimit) frm = 0.5 * (ylimit - y1) / (y - y1);
    if (y <= ylimit) frm = 0.5 + 0.5 * (ylimit - y) / (y2 - y);
  }

  //data['97a-'+i] = frm;
}

for (var i=1; i<13; i++) { data['98-'+i] = 0.024 * (data['97-'+i] - data['95-'+i]) * data['41-'+i] * data['97a-'+i]; }

data['98'] = 0; for (var i=1; i<13; i++) { data['98'] += data['98-'+i]; }

data['99'] = data['98'] / data['4'];

data['202'] = 1 - data['201'];

data['204'] = data['202'] * (1 - data['203']);

data['205'] = data['202'] * data['203'];

for (var i=1; i<13; i++) { data['211-'+i] = data['98-'+i] * data['204'] * 100 / data['206']; }

data['211'] = 0; for (var i=1; i<13; i++) { data['211'] += data['211-'+i]; }

if (data['207']>0){
  for (var i=1; i<13; i++) { data['213-'+i] = data['98-'+i] * data['205'] * 100 / data['207']; }
  data['213'] = 0; for (var i=1; i<13; i++) { data['213'] += data['213-'+i]; }
}

if (data['208']>0){
  for (var i=1; i<13; i++) { data['215-'+i] = data['98-'+i] * data['201'] * 100 / data['208']; }
  data['215'] = 0; for (var i=1; i<13; i++) { data['215'] += data['215-'+i]; }
}

for (var i=1; i<13; i++) { data['219-'+i] = data['64-'+i] * 100 / data['217-'+i]; }
data['219'] = 0; for (var i=1; i<13; i++) { data['219'] += data['219-'+i]; }

data['231'] = data['230a'] + data['230b'] + data['230c'] + data['230d'] + data['230e'] + data['230f'] + data['230g'];
data['232'] = data['L8'];

data['240'] = data['211'] * data['240b'] * 0.01;
data['241'] = data['213'] * data['241b'] * 0.01;
data['242'] = data['215'] * data['242b'] * 0.01;

data['244'] = 1 - data['243'];

data['245'] = data['219'] * data['243'] * data['245a'] * 0.01;
data['246'] = data['219'] * data['244'] * data['246a'] * 0.01;
data['247'] = data['219'] * data['247a'] * 0.01;

data['248'] = data['221'] * data['248a'] * 0.01;
data['249'] = data['231'] * data['249a'] * 0.01;

data['250'] = data['232'] * data['250a'] * 0.01;

data['255'] = data['240'] + data['241'] + data['242'] + data['245'] + data['246'] + data['247'] + data['248'] + data['249'] + data['250'] + data['251'];

data['257'] = (data['255'] * data['256']) / (data['4'] + 45.0);

var ECF = data['257'];

if (ECF >= 3.5) data['258'] = 117 - 121 * (Math.log(ECF) / Math.LN10);
if (ECF < 3.5) data['258'] = 100 - 13.95 * ECF;




/*

Lighting 

*/

data['L1'] = 59.73 * Math.pow((data['4'] * data['42']),0.4714);

data['L2'] = 1 - (0.50 * data['LLE'] / data['L']);

var windows = data['window'];

var sum = 0;
for (z in windows) 
{
  var overshading = windows[z].overshading;
  var accessfactor = [0.5,0.67,0.83,1.0];
  sum += 0.9 * windows[z].area * windows[z].g * windows[z].ff * accessfactor[overshading];
}

data['L5'] = sum / data['4'];

if (data['L5']<=0.095) {
  data['L3'] = 52.2 * Math.pow(data['L5'],2) - 9.94 * data['L5'] + 1.433;
} else {
  data['L3'] = 0.96;
}

data['L6'] = data['L1'] * data['L2'] * data['L3'];

for (var i=1; i<13; i++) { data['L7-'+i] = data['L6'] * (1.0 + (0.5 * Math.cos((2*Math.PI * (i - 0.2)) / 12.0))) * data['41-'+i] / 365.0;}

data['L8'] = 0; for (var i=1; i<13; i++) data['L8'] += data['L7-'+i];

for (var i=1; i<13; i++) { data['L9-'+i] = data['L7-'+i] * 0.85 * 1000 / (24 * data['41-'+i]); }
for (var i=1; i<13; i++) { data['L9a-'+i] = 0.4 * data['L9-'+i]; }
for (var i=1; i<13; i++) { data['67-'+i] = data['L9-'+i]; }

/*

Electrical appliances

*/

data['L10'] = 207.8 * Math.pow((data['4'] * data['42']),0.4714);

for (var i=1; i<13; i++) { data['L11-'+i] = data['L10'] * (1.0 + (0.157 * Math.cos((2*Math.PI * (i - 1.78)) / 12.0))) * data['41-'+i] / 365.0;}

data['L12'] = 0; for (var i=1; i<13; i++) data['L12'] += data['L11-'+i];

for (var i=1; i<13; i++) { data['L13-'+i] = data['L11-'+i] * 1000 / (24 * data['41-'+i]); }
for (var i=1; i<13; i++) { data['L13a-'+i] = 0.67 * data['L13-'+i]; }
for (var i=1; i<13; i++) { data['68-'+i] = data['L13-'+i]; }

data['L14'] =  (data['L12'] * 0.522 ) / data['4'];

/*

Cooking

*/

data['L15'] = 35 + 7 * data['42']; 

data['L15a'] = 23 + 5 * data['42']; 

data['L16'] = (119 + 24 * data['42']) / data['4'];

return data; 
}

