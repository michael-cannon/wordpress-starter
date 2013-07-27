#!/bin/bash

NEW_ABBR="cbqe_"
NEW_CLASS="Custom_Bulk_Quick_Edit"
NEW_FILTER="custom_bulk_quick_edit"
NEW_KB_PATH="20112546-Custom-Bulk-Quick-Edit"
NEW_SITE=""
NEW_SLUG="custom-bulk-quick-edit"
NEW_TITLE="Custom Bulk/Quick Edit"

OLD_ABBR="wps_"
OLD_CLASS="WordPress_Starter"
OLD_FILTER="wordpress_starter"
OLD_KB_PATH="20102742-WordPress-Starter-Plugin"
OLD_SITE="http://wordpress.org/extend/plugins/wordpress-starter/"
OLD_SLUG="wordpress-starter"
OLD_TITLE="WordPress Starter"

echo
echo "Begin converting ${OLD_TITLE} to ${NEW_TITLE} plugin"

FILES=`find . -type f \( -name "*.md" -o -name "*.php" -o -name "*.txt" -o -name "*.xml" \)`
for FILE in ${FILES} 
do
	perl -pi -e "s#${OLD_ABBR}#${NEW_ABBR}#g" ${FILE}
	perl -pi -e "s#${OLD_CLASS}#${NEW_CLASS}#g" ${FILE}
	perl -pi -e "s#${OLD_FILTER}#${NEW_FILTER}#g" ${FILE}
	perl -pi -e "s#${OLD_KB_PATH}#${NEW_KB_PATH}#g" ${FILE}
	perl -pi -e "s#${OLD_SLUG}#${NEW_SLUG}#g" ${FILE}
	perl -pi -e "s#${OLD_TITLE}#${NEW_TITLE}#g" ${FILE}

	if [[ '' != ${NEW_SITE} ]]
	then
		perl -pi -e "s#${OLD_SITE}#${NEW_SITE}#g" ${FILE}
	fi
done

if [[ -e 000-code-qa.txt ]]
then
	rm 000-code-qa.txt
fi

mv ${OLD_SLUG}.css ${NEW_SLUG}.css
mv ${OLD_SLUG}.php ${NEW_SLUG}.php
mv languages/${OLD_SLUG}.pot languages/${NEW_SLUG}.pot
mv lib/class-${OLD_SLUG}-settings.php lib/class-${NEW_SLUG}-settings.php

if [[ -e .git ]]
then
	rm -rf .git
fi

git init
git add *
git commit -m "Initial plugin creation"

# rm ${0}