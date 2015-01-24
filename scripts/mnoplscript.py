#Script reading MNO prefixes from UKE (Polish Office of Electronic Communication) XML file

from lxml import etree
import re


uke_prefixes = etree.parse('/Users/mateuszkrasucki/Sites/uke.xml')
mno_file = open('/Users/mateuszkrasucki/Sites/PL.json', "w+");
mno_file_empty = True;

plmns = etree.iterwalk(uke_prefixes, tag="plmn")
for plmn_action, plmn_elem in plmns:
	mno = plmn_elem.find("operator");
	if mno != None:
		mno = mno.text;
		prefix = plmn_elem.find("numer");
		if prefix != None:
			prefix = prefix.text;
			digit_count = len(prefix);
			non_digit_match = re.search('\D',prefix)
			if non_digit_match != None:
				prefix = prefix.replace("(","[");
				prefix = prefix.replace(")","]");
				if re.match(".*-0.*", prefix)!= None:
					prefix = prefix.replace("-0","-9,0");
				digit_count = non_digit_match.start() + 1;
					
			if mno_file_empty:
				mno_file_empty = False;
				mno_info_to_write = '{ "source": "http://www.uke.gov.pl/tablice/xml/T2-PLMN_T9-MVNO.xml.zip", "prefixes": [';
				mno_info_to_write = mno_info_to_write + '["\/^' + prefix + '\\\\d{' + str(9 - digit_count) + '}$\/", "' + mno + '"]';
			else:
				mno_info_to_write = ', ["\/^' + prefix + '\\\\d{' + str(9 - digit_count) + '}$\/", "' + mno + '"]';
			mno_file.write(mno_info_to_write.encode('UTF-8'));
		
mno_file.write(']}'.encode('UTF-8'));		

