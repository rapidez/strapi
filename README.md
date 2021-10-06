# Rapidez Strapi

## Requirements

A [Strapi](https://strapi.io) instance running, configured and filled with content types.

## Installation

```
composer require rapidez/strapi
```

Add the Strapi url to your `.env`:
```
STRAPI_URL=http://localhost:1337
```

### Routes

Register some routes within `routes/web.php` with the content types you'd like. The first parameter is the endpoint, the second the view to render and the last one to only get the first result from the response. For example:
```php
Route::get('blog', function ($location) {
    return (new StrapiController)('blogs', 'blog');
});

Route::get('blog/{slug}', function ($slug) {
    return (new StrapiController)('blogs?slug='.$slug, 'blog-item', true);
});
```
All data will be available within the `$data` variable in the view. For example with `{{ $data->name }}` you'll get the content of the name field.

### Dynamic zones

When you're using a dynamic zone within your content type you can render them with a Blade directive:
```
@dynamiczone($data->content)
```
This tries to render views with the same name as the component and the data will be available with the `$data` variable.

## Cache

By default all responses from Strapi will be cached for 1 hour. You can change that with `STRAPI_CACHE` in your `.env`

### Automatic cache clearing

You can setup a webhook within Strapi which will be called when something changes. If you configure that with the cache clear url from Rapidez you don't have to worry about content not showing up after changes. See the [Rapidez cache docs](https://docs.rapidez.io/0.x/cache.html).

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
