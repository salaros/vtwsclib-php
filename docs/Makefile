# Minimal makefile for Sphinx documentation
#

# You can set these variables from the command line.
SPHINXOPTS     =
SPHINXBUILD    = sphinx-build
SPHINXPROJ     = vTigerWebServicesClientPHP
SOURCEDIR      = .
BUILDDIR       = _build

ROOT_DIR 		:= $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
PHP            = php
PHPSRC         = ../src
NAMESPACE	   = Salaros\\Vtiger\\VTWSCLib
NAMESPACEPATH  = Salaros/Vtiger/VTWSCLib
VENDORDIR     := $(abspath ../vendor )
SPHPDOX        = $(VENDORDIR)/sphpdox/sphpdox

# Put it first so that "make" without argument is like "make help".
help:
	@$(SPHINXBUILD) -M help "$(SOURCEDIR)" "$(BUILDDIR)" $(SPHINXOPTS) $(O)

.PHONY: help Makefile

# Catch-all target: route all unknown targets to Sphinx using the new
# "make mode" option.  $(O) is meant as a shortcut for $(SPHINXOPTS).
%: sphpdox Makefile
	@$(SPHINXBUILD) -M $@ "$(SOURCEDIR)" "$(BUILDDIR)" $(SPHINXOPTS) $(O)

sphpdox:
	@ln -sfv $(VENDORDIR)/ $(SPHPDOX)/
	@$(PHP) -f $(SPHPDOX)/sphpdox.php process $(NAMESPACE) $(PHPSRC) -o $(BUILDDIR)
	@mv -v $(BUILDDIR)/$(NAMESPACEPATH)/* ./
	@rm -rf $(BUILDDIR)/./*
