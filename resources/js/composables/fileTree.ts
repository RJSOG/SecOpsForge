import axios from 'axios';
import Echo from 'laravel-echo';
import { ref, Ref } from 'vue';
import { route } from 'ziggy-js';

export class FileTree {
    private static _instance: FileTree;
    private echo: Echo<any>;
    private _tree: Ref<any[]>;
    private _path: Ref<string>;

    private constructor() {
        this._path = ref('');
        this._tree = ref([]);

        this.echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
            withCredentials: true,
        });
    }

    public static get instance(): FileTree {
        if (!FileTree._instance) {
            FileTree._instance = new FileTree();
        }

        return FileTree._instance;
    }

    public updateInstance(path: Ref<string>, tree: Ref<any[]>) {
        this._path = path;
        this._tree = tree;
    }

    async fetch(currentPath: string, format: string): Promise<void> {
        try {
            await axios.post(
                route('api.build.file.tree'),
                {
                    root: currentPath,
                    format: format,
                },
                { withCredentials: true },
            );

            this.echo
                .private('build.file.tree')
                .listen('.completed', (event: any) => {
                    const data = JSON.parse(event.message);
                    if (data.root === this._path.value) {
                        this._tree.value = data.tree;
                        localStorage.setItem(
                            this._path.value,
                            JSON.stringify(data),
                        );
                    }
                })
                .listen('.failed', () => {
                    console.error('Failed to fetch file tree');
                    this._tree.value = [];
                });
        } catch (err) {
            console.error(err);
        }
    }

    async isUpToDate(
        root: string,
        storedTree: string | null,
    ): Promise<boolean> {
        if (storedTree == null) return false;

        try {
            const response = await axios.post(
                route('api.validate.file.tree'),
                {
                    root: root,
                    hash: JSON.parse(storedTree).hash,
                },
                { withCredentials: true },
            );

            return response.data.is_valid;
        } catch (err) {
            console.error(err);
            return false;
        }
    }

    get path() {
        return this._path;
    }

    set path(value: Ref<string>) {
        this._path = value;
    }
}
