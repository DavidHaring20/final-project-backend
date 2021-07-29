
FROM alpine:3.13.2

COPY . /code
COPY docker/. /

ARG BUILD=debug
RUN chmod +x /setup && /setup

EXPOSE 80

ENV PS1 '\[\e[1;36m\]\u\[\e[1;37m\]@\[\e[1;33m\]pocket-api-\h\[\e[1;37m\]:\[\e[1;34m\]\w\[\e[1;37m\]\$\[\e[0m\] '
ENTRYPOINT /start
