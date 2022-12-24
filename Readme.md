# Wordpress Cinco.

## Getting Started

This wordpress is for replace a Vue application blog with wordpress block that requieres less mainteinance and it's easy to update.

### Prerequisites

For run the development server you need to installs.

- docker
- docker-compose

### Installation

#### Development server

1. Clone the repo
   ```sh
   git clone https://github.com/your_username_/Project-Name.git
   ```
2. after the installation of docker and docker compose
   ```sh
   docker-compose up
   ```

#### Production server

1. Build the docker image.
   ```sh
   make build
   ```
2. push to your registry
   ```sh
   docker tag unoraya/cinco youregistry.com/repository
   docker push youregistry.com/repository
   ```
3. Restore database in Aurora.

4. Execute query for update the field that correspond to new site url
   ```sh
    update fdsw_options set option_value='https://your_new_site_url.com/' where option_name='siteurl';
    update fdsw_options set option_value='https://your_new_site_url.com/' where option_name='home';
   ```
5. Deploy ECS with terraform and update the with the new registry URL

6. Enjoy

7. Creado por Uno Raya
