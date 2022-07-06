FROM amazonlinux:2

LABEL version="1.0"
LABEL description="amazonlinux2にnginxを入れて立ち上げ"

RUN amazon-linux-extras install -y nginx1

COPY ./cloudnative-hands-on/conf/index.html /usr/share/nginx/html/
COPY ./cloudnative-hands-on/conf/yoshi.jpeg  /usr/share/nginx/html/

CMD ["nginx", "-g", "daemon off;"]
