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

You must set the name of your frontend environment (e.g. 'ng', 'quest', ...) in config.yml:

```
genj_frontend_url:
    frontend_environment: dv
```


## Usage

### Front-end URL generation

From twig:

	{{ object|genj_url_for_frontend }}
	
From PHP:

        $urlGenerator = $this->container->get('genj_url_generator.routing.frontend.generator.url_generator');
        $frontendUrl  = $urlGenerator->generateFrontendUrlForObject($object, $preview);
