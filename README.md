# GenjFrontendUrlBundle

Provides helpers to generate frontend URLs. Your Entities need these methods in order for this bundle to work:

```
    public function getRouteName()
    {
        return 'genj_article_article_show';
    }

    public function getRouteParameters()
    {
        return array(
            'categoryType' => $this->getCategory()->getType(),
            'categorySlug' => $this->getCategory()->getSlug(),
            'slug'         => $this->getSlug()
        );
    }	
```


## Configuration

You must set the name of your frontend environment in config.yml:

```
genj_frontend_url:
    frontend_environment: my_frontend_app
```


## Usage

### Front-end URL generation

From twig:

```
{{ object|genj_url_for_frontend }}
```
	
From PHP:

```
$urlGenerator = $this->container->get('genj_url_generator.routing.frontend.generator.url_generator');
$frontendUrl  = $urlGenerator->generateFrontendUrlForObject($object);
```

### Preview parameter

It is possible to generate a URL to a 'preview controller'. You could e.g. restrict access to that controller and show non-cached versions of certain pages. If you do:

```
$frontendUrl  = $urlGenerator->generateFrontendUrlForObject($object);
```

Then the resulting URL would become /preview.php/path/to/page.

You can use the same thing from Twig too:

```
{{ object|genj_url_for_frontend(true) }}
```

More about environments: http://symfony.com/doc/current/cookbook/configuration/environments.html
