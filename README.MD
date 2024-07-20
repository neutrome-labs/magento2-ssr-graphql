    There should not be one to choose between PHP and GraphQL. 
    Revolutionize your customer's experience with the best from both.

# Magento 2 SSR GraphQL Module

Adds SSR GraphQL resolving capabilities to any Magento 2 frontend.
Compatible with any theme. Works best with AlpineJS.


## Overview

The idea is to write a normal GraphQL `query`, (which will be used on the frontend
via JS,) AND automatically preload it's `response` at the page render,
providing JS code with instant `data`/`errors` - without executing the "first" Ajax request.

Then, JS should operate as normal, with the access both to the `query` string,
`ssr` data (variables, response) and simplified `fetch()`, to repeat the query, optionally passing new parameters.


## Where To Use

Anywhere on your pages, when the <i>same</i> [available via GraphQL] content
needs to be available to the customer at the page render time (PHP),
while could be updated later (JS).

Typical use-cases:
* pagination/lazy-load - the first set of the items could be preloaded at the page render,
  and paginated later, with the same GraphQL `query` and different `variables`
* double-check - the "Add To Cart" button is rendered if the product is available for sale,
  and the same query is `refresh`-ed on click, to prevent stock races

## How To Use

Simple example - paginated product list, over GraphQL.

1. The `phtml` view must call the `makeSsrGqlCall(query, variables)` method, 
   from the `NeutromeLabs\SsrGraphql\ViewModel\SsrGraphqlViewModel` view model,
   which returns JS fragment string representing `object`, to insert into the page html script tag.
```
var ssrGqlExampleObject = <?= $gql->makeSsrGqlCall(<<<GRAPHQL
query productsQuery(\$currentPage: Int!) {
    products (search: "", pageSize: 3, currentPage: \$currentPage) {
        items {
            sku
        }
    }
}
GRAPHQL, ['currentPage' => 1]) ?>;
renderData(); // some logic to present ssrGqlExampleObject to the customer
```

2. While rendering this page, Magento will resolve the passed query and provide
   `ssrGqlExampleObject` JS variable with a special SSR GraphQL `object`
```
{
    // indicates, if the response is SSR-generated. true, if no "refresh" calls were made
    fresh: boolean,
    // "current" response data
    data?: object,
     // "current" response errors
    errors?: object[],
    ssr: {
        query: string, // GraphQL query string
        variables: object, // GraphQL variables, passed at the step 1
        response: object, // SSR GraphQL response
    },
    // fetches the same query with the merged variables from the ssr.variables and parameter
    fetch: (variables?: object) => Promise<object>, 
    // fetch(), then update the inner data and errors fields with the new response
    refresh: (variables?: object) => Promise<void>,
}
```

3. The data from the JS variable could be presented to the customer right after the page load,
   with no "loaders" on the screen. Then those data could be updated, without the need
   to repeat the same GraphQL query again
```
currentPage++;

ssrGqlExampleObject.refresh({
    currentPage: currentPage
}).then(() => {
    renderData();
})
```

See the full example at `https://your-magento-instance.test/ssrgql/example`,
and [src/SsrGraphql/view/frontend/templates/example/1.phtml](src/SsrGraphql/view/frontend/templates/example/1.phtml)