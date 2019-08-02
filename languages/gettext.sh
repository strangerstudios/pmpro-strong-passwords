#---------------------------
# This script generates a new pmpro.pot file for use in translations.
# To generate a new pmpro-strong-passwords.pot, cd to the main /pmpro-strong-passwords/ directory,
# then execute `languages/gettext.sh` from the command line.
# then fix the header info (helps to have the old pmpro.pot open before running script above)
# then execute `cp languages/pmpro-strong-passwords.pot languages/pmpro-strong-passwords.po` to copy the .pot to .po
# then execute `msgfmt languages/pmpro-strong-passwords.po --output-file languages/pmpro-strong-passwords.mo` to generate the .mo
#---------------------------
echo "Updating pmpro-strong-passwords.pot... "
xgettext -j -o languages/pmpro-strong-passwords.pot \
--default-domain=pmpro-strong-passwords \
--language=PHP \
--keyword=_ \
--keyword=__ \
--keyword=_e \
--keyword=_ex \
--keyword=_n \
--keyword=_x \
--sort-by-file \
--package-version=1.0 \
--msgid-bugs-address="info@paidmembershipspro.com" \
$(find . -name "*.php")
echo "Done!"