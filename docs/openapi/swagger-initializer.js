window.onload = function() {
  //<editor-fold desc="Changeable Configuration Block">
  const DisableTryItOutPlugin = function() {
    return {
      statePlugins: {
        spec: {
          wrapSelectors: {
            allowTryItOutFor: () => () => false
          }
        }
      }
    }
  }

  // the following lines will be replaced by docker/configurator, when it runs in a docker-container
  window.ui = SwaggerUIBundle({
    url: "./docs.jsonopenapi",
    dom_id: '#swagger-ui',
    tryItOutEnabled: false,
    filter: false,
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl,
      DisableTryItOutPlugin
    ],
    layout: "StandaloneLayout",
  });

  //</editor-fold>
};
