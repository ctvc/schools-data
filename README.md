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


## Getting other data

For [Scotland](http://www.gov.scot/Topics/Statistics/Browse/School-Education/Datasets/contactdetails) 
data you need to remove the columns from the Excel that have duplicated headers.
The CSV parser requires each header be unique. There are two columns called primary, secondary, and special.
Remove them, then remove the extra rows above and below the table, and export as CSV.

The data for [Ireland](https://www.education.ie/en/Publications/Statistics/Data-on-Individual-Schools/) 
comes in 3 separate sheets. You could remove the headers from two of them and concatenate them to the end of the other.
Or, run 3 separate imports.
