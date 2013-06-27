#!/bin/bash

NEW_CLASS="Custom_Bulk_Quick_Edit"
NEW_FILTER="custom_bulk_quick_edit"
NEW_KB_PATH="20112546-Custom-Bulk-Quick-Edit"
NEW_SLUG="custom-bulk-quick-edit"
NEW_TITLE="Custom Bulk/Quick Edit"

OLD_CLASS="WordPress_Starter"
OLD_FILTER="wordpress_starter"
OLD_KB_PATH="20102742-WordPress-Starter-Plugin"
OLD_SLUG="wordpress-starter"
OLD_TITLE="WordPress Starter"

echo
echo "Begin converting ${OLD_TITLE} to ${NEW_TITLE} plugin"

FILES=`find . -type f \( -name "*.md" -o -name "*.php" -o -name "*.txt" -o -name "*.xml" \)`
for FILE in ${FILES} 
do
	perl -pi -e "s#${OLD_CLASS}#${NEW_CLASS}#g" ${FILE}
	perl -pi -e "s#${OLD_FILTER}#${NEW_FILTER}#g" ${FILE}
	perl -pi -e "s#${OLD_KB_PATH}#${NEW_KB_PATH}#g" ${FILE}
	perl -pi -e "s#${OLD_SLUG}#${NEW_SLUG}#g" ${FILE}
	perl -pi -e "s#${OLD_TITLE}#${NEW_TITLE}#g" ${FILE}
done

mv ${OLD_SLUG}.css ${NEW_SLUG}.css
mv ${OLD_SLUG}.php ${NEW_SLUG}.php
mv languages/${OLD_SLUG}.pot languages/${NEW_SLUG}.pot
mv lib/class-${OLD_SLUG}-settings.php lib/class-${NEW_SLUG}-settings.php
rm ${0}