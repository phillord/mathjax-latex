
VERSION=0.1
FILES=mathjax-latex.php license.txt COPYING readme.txt js
CP=cp -r

SVN_WORK = $(HOME)/subversion-repo/wordpress-updateable/mathjax-latex/trunk

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


svn-publish: 
	$(CP) $(FILES) $(SVN_WORK)
