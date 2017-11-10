# Schools Data

Create an autocomplete field in Elasticsearch 
for the list of UK schools.

## Install

 - Run `composer install`.
 - Copy `.env-example` to `.env` and make the required changes.
 - Download a [CSV of UK Schools data](https://get-information-schools.service.gov.uk/) and add the path to the file to the configuration in 
`.env`

## Usage

Configure the Elasticsearch index

    ./bin/schools index
    
Then run the batch importer:

    ./bin/schools import
