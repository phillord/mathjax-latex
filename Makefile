
include ../../publish/makefile_conf.inc


DIR=../mathjax-latex/
VERSION=0.1
FILES=$(DIR)MathJax/ $(DIR)mathjax-latex.php $(DIR)license.txt\
	$(DIR)readme.txt 


all:
	$(MAKE) -C .. mathjax-latex fix_perm

publish:
	$(CP) ../mathjax-latex $(PLUGINS)


package:
	tar cvfz mathjax-latex-$(VERSION).tgz $(FILES)


publish-kb:
	scp mathjax-latex-$(VERSION).tgz knowledgeblog.org:

test: all
	- rm index.html
	wget http://yarm.ncl.ac.uk

include ../../publish/fix_perm.inc