#!/bin/bash

NEW_ABBR="cbqe_"
NEW_BASE="custom-bulk-quick-edit"
NEW_CLASS="Custom_Bulk_Quick_Edit"
NEW_FILTER="${NEW_ABBR}"
NEW_KB_PATH="20112546-Custom-Bulk-Quick-Edit"
NEW_SITE=""
NEW_SLUG="CBQE_"
NEW_TITLE="Custom Bulk/Quick Edit"

OLD_ABBR="wps_"
OLD_BASE="wordpress-starter"
OLD_CLASS="WordPress_Starter"
OLD_FILTER="wordpress_starter"
OLD_KB_PATH="20102742-WordPress-Starter-Plugin"
OLD_SITE="http://wordpress.org/plugins/wordpress-starter/"
OLD_SLUG="WPS_"
OLD_TITLE="WordPress Starter"

echo
echo "Begin converting ${OLD_TITLE} to ${NEW_TITLE} plugin"

FILES=`find . -type f \( -name "*.md" -o -name "*.php" -o -name "*.txt" -o -name "*.xml" \)`
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
	fi

	if [[ '' != ${NEW_TITLE} ]]
	then
		perl -pi -e "s#${OLD_TITLE}#${NEW_TITLE}#g" ${FILE}
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
git add .gitignore
git add .travis.yml
git commit -m "Initial plugin creation"
echo "git remote add origin git@github.com:michael-cannon/${NEW_BASE}.git"

git remote add aihrus git@github.com:michael-cannon/aihrus-framework.git
git fetch aihrus 
git subtree add -P lib/aihrus --squash aihrus master
git commit -a -m "Add in aihrus framework"
git push origin master
echo "git push origin master"