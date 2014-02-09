#!/bin/bash

NEW_ABBR="CBQE_"
NEW_BASE="custom-bulk-quick-edit"
NEW_CLASS="Custom_Bulk_Quick_Edit"
NEW_FILTER="${NEW_ABBR,,}"
NEW_KB_PATH="20112546"
NEW_SITE=""
NEW_SLUG="${NEW_FILTER}"
NEW_SLUG_LONG="${NEW_BASE/-/_}"
NEW_TITLE="Custom Bulk/Quick Edit"
NEW_TITLE_SHORT="${NEW_TITLE}"

OLD_ABBR="WPS_"
OLD_BASE="wordpress-starter"
OLD_CLASS="WordPress_Starter"
OLD_FILTER="${OLD_ABBR,,}"
OLD_KB_PATH="20102742"
OLD_SITE="http://wordpress.org/plugins/${OLD_BASE}/"
OLD_SLUG="${OLD_FILTER}"
OLD_SLUG_LONG="${OLD_BASE/-/_}"
OLD_TITLE="WordPress Starter"
OLD_TITLE_SHORT="${OLD_TITLE}"

echo
echo "Begin converting ${OLD_TITLE} to ${NEW_TITLE} plugin"

FILES=`find . -type f \( -name "*.css" -o -name "*.md" -o -name "*.php" -o -name "*.txt" -o -name "*.xml" \)`
for FILE in ${FILES} 
do
	if [[ '' != ${NEW_ABBR} ]]
	then
		perl -pi -e "s#${OLD_ABBR}#${NEW_ABBR}#g" ${FILE}
		perl -pi -e "s#${NEW_ABBR}_#${NEW_ABBR}#g" ${FILE}
	fi

	if [[ '' != ${NEW_BASE} ]]
	then
		perl -pi -e "s#${OLD_BASE}#${NEW_BASE}#g" ${FILE}
	fi

	if [[ '' != ${NEW_CLASS} ]]
	then
		perl -pi -e "s#${OLD_CLASS}#${NEW_CLASS}#g" ${FILE}
	fi

	if [[ '' != ${NEW_FILTER} ]]
	then
		perl -pi -e "s#${OLD_FILTER}#${NEW_FILTER}#g" ${FILE}
	fi

	if [[ '' != ${NEW_KB_PATH} ]]
	then
		perl -pi -e "s#${OLD_KB_PATH}#${NEW_KB_PATH}#g" ${FILE}
	fi

	if [[ '' != ${NEW_SITE} ]]
	then
		perl -pi -e "s#${OLD_SITE}#${NEW_SITE}#g" ${FILE}
	fi

	if [[ '' != ${NEW_SLUG} ]]
	then
		perl -pi -e "s#${OLD_SLUG}#${NEW_SLUG}#g" ${FILE}
		perl -pi -e "s#${NEW_SLUG}_#${NEW_SLUG}#g" ${FILE}
	fi

	if [[ '' != ${NEW_SLUG_LONG} ]]
	then
		perl -pi -e "s#${OLD_SLUG_LONG}#${NEW_SLUG_LONG}#g" ${FILE}
	fi

	if [[ '' != ${NEW_TITLE} ]]
	then
		perl -pi -e "s#${OLD_TITLE}#${NEW_TITLE}#g" ${FILE}
	fi

	if [[ '' != ${NEW_TITLE_SHORT} ]]
	then
		perl -pi -e "s#${OLD_TITLE_SHORT}#${NEW_TITLE_SHORT}#g" ${FILE}
	fi
done

if [[ -e 000-code-qa.txt ]]
then
	rm 000-code-qa.txt
fi

mv ${OLD_BASE}.php ${NEW_BASE}.php
mv assets/css/${OLD_BASE}.css assets/css/${NEW_BASE}.css
mv includes/class-${OLD_BASE}-settings.php includes/class-${NEW_BASE}-settings.php
mv includes/class-${OLD_BASE}-widget.php includes/class-${NEW_BASE}-widget.php
mv includes/class-${OLD_BASE}.php includes/class-${NEW_BASE}.php
mv languages/${OLD_BASE}.pot languages/${NEW_BASE}.pot

if [[ -e .git ]]
then
	rm -rf .git
fi

LIB_AIHRUS="includes/libraries/aihrus"
if [[ -e ${LIB_AIHRUS} ]]
then
	rm ${LIB_AIHRUS}
fi

git init
git add *
git add .gitignore
git commit -m "Initial plugin creation"
git remote add origin git@github.com:michael-cannon/${NEW_BASE}.git
echo "git push origin master"