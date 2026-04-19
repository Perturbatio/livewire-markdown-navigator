# Document Navigator

This is a simple livewire component that allows you to view markdown documentation stored in
a given disk and path.

Usage:
```blade
    <livewire:markdown-navigator
        doc-path="markdown-navigator"
        diskName="docs"
    />
```

```blade
    <livewire:markdown-navigator
        doc-path="markdown-navigator"
        diskName="docs"
        cacheDuration="30"
        loadingClasses="custom-loading classes blur-sm"
        startingDepth="2"
        :collapseChildren="true"
    />
```

Parameters:

| Parameter          | Description                                                                                                       | Optional? | Default                                                     |
|--------------------|-------------------------------------------------------------------------------------------------------------------|-----------|-------------------------------------------------------------|
| `doc-path`         | The path where the markdown files you<br/>want to render are located on the disk.                                 | No        | N/A                                                         |
| `cacheDuration`    | Time in minutes to retain the cache                                                                               | Yes       | 60                                                          |
| `diskName`         | The name of the disk where the markdown files are stored.                                                         | Yes       | 'docs'                                                      |
| `loadingClasses`   | classes applied to the content area when loading                                                                  | Yes       | 'loading opacity-75 transition-opacity pointer-events-none' |
| `defaultFile`      | The path of the markdown file to be selected on initial load. (absolute from disk base)                           | Yes       | null                                                        |
| `startingDepth`    | The depth at which the documents will be rendered from (this allows you to scope the rendering to deeper levels). | Yes       | 1                                                           |
| `collapseChildren` | Whether to collapse child nodes in the navigator.                                                                 | Yes       | false                                                       |
