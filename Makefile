
# Basic Commands

build:
	docker build -t unoraya/cinco -f compose/Dockerfile.prod --no-cache .
	docker tag unoraya/cinco:latest 306809727018.dkr.ecr.us-east-1.amazonaws.com/cinco-prod-cinco:latest
	docker push 306809727018.dkr.ecr.us-east-1.amazonaws.com/cinco-prod-cinco:latest
	
