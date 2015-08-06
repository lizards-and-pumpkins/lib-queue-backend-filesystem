Brera Queue Library
===

A simple pipeline (worker) queue library.
The contained classes all implement the `\Brera\Queue\Queue` interface.

**Note:**  
The `Brera\File\FileQueue` uses the PHP function `flock` for locking. That function is known to have issues when used on NFS mounts.
Because of this, the file queue implementation will only work reliably if all processes reside on the same host and use
the same local file system
