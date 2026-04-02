# Document Navigator

This is a simple livewire component that allows you to view markdown documentation stored in
a given disk and path.

Usage:
```blade
    <livewire:markdown-navigator
        doc-path="markdown-navigator"
        diskName="docs"
        cacheDuration="30"
        defaultContent => <<<MARKDOWN
    # Some default markdown content
    
    To be displayed when the component is first loaded and
    there is no selected file detected.
    <<<MARKDOWN,
    />
```

Parameters:

| Parameter        | Description                                                                       | Optional? | Default            |
|------------------|-----------------------------------------------------------------------------------|-----------|--------------------|
| `cacheDuration`  | Time in minutes to retain the cache                                               | Yes       | 60                 |
| `defaultContent` | Markdown content to display before any file is selected                           | Yes       | basic instructions |
| `diskName`       | The name of the disk where the markdown files are stored.                         | Yes       | 'docs'             |
| `doc-path`       | The path where the markdown files you<br/>want to render are located on the disk. | No        | N/A                |

