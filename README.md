### Cloud Pub/Sub

<img src="https://travis-ci.org/gdg-tangier/cloud-pubsub.svg?branch=master">
<img src="https://github.styleci.io/repos/206420540/shield?branch=master">

Google Cloud Pub/Sub for laravel.

### Testing

You need to install [GCP command line tool](https://cloud.google.com/sdk/gcloud/).

1. Run the pubsub emulator`./emulator.sh`
2. Export the pubsub emulator host `export PUBSUB_EMULATOR_HOST=localhost:8085`
3. Run `phpunit`
