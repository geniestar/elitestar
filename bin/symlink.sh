#/bin/bash
echo 'removing exist symlink...';
rm /var/www/html/elitestar -rf;
rm /usr/share/pear/elitestar -rf;

mkdir /usr/share/pear/elitestar;

echo 'building symlink...';
ln -s /home/ec2-user/git/elitestar/src/lib /usr/share/pear/elitestar/lib;
ln -s /home/ec2-user/git/elitestar/conf /usr/share/pear/elitestar/conf;
ln -s /home/ec2-user/git/elitestar/src/frontend/templates /usr/share/pear/elitestar/templates;
ln -s /home/ec2-user/git/elitestar/src/frontend/dataProcess /usr/share/pear/elitestar/dataProcess;
ln -s /home/ec2-user/git/elitestar/src/frontend/html/ /var/www/html/elitestar;
ln -s /home/ec2-user/git/elitestar/src/static/css /var/www/html/elitestar/css;
ln -s /home/ec2-user/git/elitestar/src/static/js /var/www/html/elitestar/js;
ln -s /home/ec2-user/git/elitestar/src/static/img /var/www/html/elitestar/img;
