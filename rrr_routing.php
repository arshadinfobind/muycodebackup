# rrr_api.example:
#   path: '/rrr-api/api2/subscriptions'
#   defaults:
#     _title: 'Example'
#     _controller: '\Drupal\rrr_api\Controller\RrrApiController::build'
#   requirements:
#     _permission: 'access content'
# rrr_http_log.search:
#   path: "/rrr-http-log-search"
#   defaults:
#     _controller: '\Drupal\rrr_http_log\Controller\HttpLogController::searchForm'
#     _title: "Search Entries by Time"
#     _form: '\Drupal\rrr_http_log\form\SearchForm'
#   requirements:
#     _permission: "access content"

rrr_http_log.Create_Form:
  path: "/rrr-http-log-search"
  defaults:
    # _controller: '\Drupal\rrr_http_log\Controller\HttpLogController::searchForm'
    _title: "Search Entries by Time"
    _form: 'Drupal\rrr_http_log\Form\CreateForm'
  requirements:
    _permission: "access content"
# rrr_http_log.rrr_crm2:
#   path: "/rrr_crm07"
#   defaults:
#     _controller: '\Drupal\rrr_http_log\Controller\HttpLogController::rrr_crm2'

#     _title: "Search Entries by Time"
#   requirements:
#     _permission: "access content"

rrr_http_log.build:
  path: "/rrr-http-log-search-build"
  defaults:
    # _controller: '\Drupal\rrr_http_log\Controller\HttpLogController::searchForm'
    _title: "Search Entries by Time"
    _controller: 'Drupal\rrr_http_log\Controller\HttpLogController::build'
  requirements:
    _permission: "access content"

rrr_http_log.New_Form:
  path: "/first"
  defaults:
    # _controller: '\Drupal\rrr_http_log\Controller\HttpLogController::searchForm'
    _title: "Search Entries by Time New"
    _form: 'Drupal\rrr_http_log\Form\NewForm'
  requirements:
    _permission: "access content"


    rrr_http_log.New_Create_Form:
  path: "/rrr_request"
  defaults:
    # _controller: '\Drupal\rrr_http_log\Controller\HttpLogController::searchForm'
    _title: "Search Entries"
    _form: 'Drupal\rrr_http_log\Form\NewCreateForm'
  requirements:
    _permission: "access content"
