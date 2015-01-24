#Script reading Country Codes from Google libphonenumber library xml files. Everything except North America. 

from lxml import etree
import re

iso_2_codes = ['AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AN', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AX', 'AZ', 'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BR', 'BS', 'BT', 'BV', 'BW', 'BY', 'BZ', 'CA', 'CC', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CU', 'CV', 'CX', 'CY', 'CZ', 'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ', 'EC', 'EE', 'EG', 'EH', 'ER', 'ES', 'ET', 'FI', 'FJ', 'FK', 'FM', 'FO', 'FR', 'GA', 'GB', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GW', 'GY', 'HK', 'HM', 'HN', 'HR', 'HT', 'HU', 'ID', 'IE', 'IL', 'IM', 'IN', 'IO', 'IQ', 'IR', 'IS', 'IT', 'JE', 'JM', 'JO', 'JP', 'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ', 'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'ME', 'MF', 'MG', 'MH', 'MK', 'ML', 'MM', 'MN', 'MO', 'MP', 'MQ', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ', 'NA', 'NC', 'NE', 'NF', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ', 'OM', 'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PS', 'PT', 'PW', 'PY', 'QA', 'RE', 'RO', 'RS', 'RU', 'RW', 'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'SZ', 'TC', 'TD', 'TF', 'TG', 'TH', 'TJ', 'TK', 'TL', 'TM', 'TN', 'TO', 'TR', 'TT', 'TV', 'TW', 'TZ', 'UA', 'UG', 'UM', 'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU', 'WF', 'WS', 'YE', 'YT', 'ZA', 'ZM', 'ZW'];

libphonenumber = etree.parse('/Users/mateuszkrasucki/Sites/libphonenumber.xml')
codes_files = [ open('/Users/mateuszkrasucki/Sites/1.json', "w+"), open('/Users/mateuszkrasucki/Sites/2.json', "w+"), open('/Users/mateuszkrasucki/Sites/3.json', "w+"), open('/Users/mateuszkrasucki/Sites/4.json', "w+"), open('/Users/mateuszkrasucki/Sites/5.json', "w+"), open('/Users/mateuszkrasucki/Sites/6.json', "w+"), open('/Users/mateuszkrasucki/Sites/7.json', "w+"), open('/Users/mateuszkrasucki/Sites/8.json', "w+"), open('/Users/mateuszkrasucki/Sites/9.json', "w+")];
codes_files_empty = [True] * 10;

codes_known = [];
codes_multi = []
territories = etree.iterwalk(libphonenumber, tag="territory")
for territory_action, territory_elem in territories:
	country_code = territory_elem.attrib["countryCode"];
	if country_code in codes_known:
		if country_code not in codes_multi:
			codes_multi.append(country_code);
	else:
		codes_known.append(country_code);

codes_problem = [];
territories = etree.iterwalk(libphonenumber, tag="territory")
for territory_action, territory_elem in territories:
	country_id = territory_elem.attrib["id"];
	if country_id in iso_2_codes:
		country_code = territory_elem.attrib["countryCode"];
		mobile_elem = territory_elem.find("mobile");
		if country_code not in codes_multi and mobile_elem.find("possibleNumberPattern") != None:
			mobile_pattern = mobile_elem.find("possibleNumberPattern").text;
			if mobile_pattern == "NA":
				mobile_pattern = "\d{" + str(15 - len(country_code)) + "}";
			if mobile_elem.find("exampleNumber") != None:
				example_number = mobile_elem.find("exampleNumber").text;
		 		if re.match(mobile_pattern, example_number) == None:
					print "Negative match for example and mobile pattern " + country_id + " " + country_code;	
					codes_problem.append(country_code);
			else:
				print "No example number for " + country_id + " " + country_code + " " + mobile_pattern + ".";	
				codes_problem.append(country_code);
		else:
			national_number_pattern = mobile_elem.find("nationalNumberPattern").text;
			national_number_pattern = national_number_pattern.replace("?:","");
			mobile_pattern = "".join(national_number_pattern.split());
			if mobile_pattern == "NA":
				mobile_pattern = "\d{" + str(15 - len(country_code)) + "}";
			if mobile_elem.find("exampleNumber") != None:
				example_number = mobile_elem.find("exampleNumber").text;
		 		if re.match(mobile_pattern, example_number) == None:
					print "Negative match for example and mobile pattern for " + country_id + " " + country_code + " " + mobile_pattern + ".";
					codes_problem.append(country_code);	
			else:
				print "No example mobile number for " + country_id + " " + country_code + " " + mobile_pattern + ".";	
				codes_problem.append(country_code);
	
		mobile_pattern = mobile_pattern.replace("\\","\\\\");
		first_digit = int(country_code[0]);
		if codes_files_empty[first_digit-1]:
			codes_files_empty[first_digit-1] = False;
			code_info_to_write = '{ "source": "https://raw.githubusercontent.com/googlei18n/libphonenumber/master/resources/PhoneNumberMetadata.xml", "codes": [';
			code_info_to_write = code_info_to_write + '["\/^' + country_code + '(' + mobile_pattern + ')$\/", "' + country_code + '", "' + country_id + '"]';
		else:
			code_info_to_write = ', ["\/^' + country_code + '(' + mobile_pattern + ')$\/", "' + country_code + '", "' + country_id + '"]';
		codes_files[first_digit-1].write(code_info_to_write.encode('UTF-8'));
		
for codes_file in codes_files:
	codes_file.write(']}'.encode('UTF-8'));		
print;			
print "Special caution should be applied to those codes because of repetition:"
print codes_multi;

print "Those codes should be checked because of lack of example number pattern or error while matching:";
print codes_problem;
