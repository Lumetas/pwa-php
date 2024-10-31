В pwa_config.php, есть класс с различными параметрами

`pwa_installed_hook` - функция которая будет выполняться при установке pwa пользователем.

`pwa_unique_get_key` - ключ значением которого будет уникальный ключ присваевыемый пользователю.

`install_pwa_function_name` - название javascript функции которую можно вызвать для установки пва на клиенте

`pwa_service_worker_code` - стандартный код сервис воркера, после которого будет вставлен код из файла указанного в manifest

`pwa_script_file` - файл регистрирующий сервис-воркер, а так же задающий некоторые функции.

Манифест немного отличается от стандартного
```
{
    "manifest": {
        "theme_color": "#8936FF",
        "background_color": "#2EC6FE",
        "icons": [
            {
                "purpose": "maskable",
                "sizes": "512x512",
                "src": "png.png",
                "type": "image/png"
            },
            {
                "purpose": "any",
                "sizes": "512x512",
                "src": "png.png",
                "type": "image/png"
            }
        ],
        "orientation": "any",
        "display": "fullscreen",
        "dir": "auto",
        "lang": "ru-RU",
        "name": "pwa",
        "short_name": "pwa",
        "start_url": "/",
        "scope": "/",
        "description": "описание"
    },
    "pwa_lib":{
        "service_worker" : "sw.js",
        "lib_url": "/pwa.php",
        "cache_files":["/"],
        "cache_version": "v2"
    }
}
```
В pwa_lib Так же содержатся основные данные для библиотеки.

`service_worker` - файл сервис-воркера

`lib_url` - url основного файла библиотеки в интернете, необходимо для хуков и т.д.

`cache_files` - файлы которые нужно закешировать сервис-воркером и версия кэша, меняя её мы будем обновлять сервис воркер
