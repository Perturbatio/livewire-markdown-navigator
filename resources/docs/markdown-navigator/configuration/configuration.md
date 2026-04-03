# Configuration

```php
return [
    'default_disk' => 'docs',
    'permitted_disks' => [
        'docs',
    ],
    'commonmark_options' => [

    ],
];
```

| Key                  | Description                                                                                                                                                                                            | Default Value |
|----------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------|
| `default_disk`       | The default disk to use when no disk is specified. Must be included in the `permitted_disks` array.                                                                                                    | 'docs'        |
| `permitted_disks`    | An array of disk names that the component is allowed to access. This is a security measure to prevent unauthorized access to files on the server.                                                      | ['docs']      |
| `commonmark_options` | An array of options to pass to the CommonMark parser. This allows you to customize the markdown rendering. For example, you can enable or disable certain markdown features, or add custom extensions. | []            |

For more information on the available CommonMark options, see the [league/commonmark documentation](https://commonmark.thephpleague.com/2.x/configuration/).

[Back to the main page](../README.md)
