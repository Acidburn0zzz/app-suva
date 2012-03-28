
Name: app-suva
Epoch: 1
Version: 1.0.10
Release: 1%{dist}
Summary: Suva - APIs and install
License: Proprietary
Group: ClearOS/Libraries
Source: app-suva-%{version}.tar.gz
Buildarch: noarch

%description
Suva provides tunnel and encryption services to ClearCenter portal

%package core
Summary: Suva - APIs and install
Requires: app-base-core
Requires: suva-client

%description core
Suva provides tunnel and encryption services to ClearCenter portal

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/suva
cp -r * %{buildroot}/usr/clearos/apps/suva/

install -d -m 0755 %{buildroot}/var/clearos/suva

%post core
logger -p local6.notice -t installer 'app-suva-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/suva/deploy/install ] && /usr/clearos/apps/suva/deploy/install
fi

[ -x /usr/clearos/apps/suva/deploy/upgrade ] && /usr/clearos/apps/suva/deploy/upgrade

exit 0

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-suva-core - uninstalling'
    [ -x /usr/clearos/apps/suva/deploy/uninstall ] && /usr/clearos/apps/suva/deploy/uninstall
fi

exit 0

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/suva/packaging
%exclude /usr/clearos/apps/suva/tests
%dir /usr/clearos/apps/suva
%dir /var/clearos/suva
/usr/clearos/apps/suva/deploy
/usr/clearos/apps/suva/language
/usr/clearos/apps/suva/libraries
