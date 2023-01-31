import { defineConfig, splitVendorChunkPlugin } from "vite";
import liveReload from 'vite-plugin-live-reload';
import path from "path";

export default defineConfig({
  // Set Vite root dir for cleaner asset manifest entries.
  root: "resources/scripts",

  plugins: [
    liveReload([
      'public/index.php',
      'app/**/*.php'
    ], {
      alwaysReload: true,
      root: process.cwd(),
    }),
    splitVendorChunkPlugin(),
  ],

  base: process.env.VITE_DEV
    ? "/" 
    : "/build/",

  //base: command === 'build'
    // Full path to this plugin, relative to server's doc root.
    //? ''
    // In dev mode, relative to Vite's own doc root.
    //: '',

  build: {
    /**
     * Vite normally copies public/* into the build dir, but since
     * we're using public/ as the web root and building to public/build,
     * the copy is redundant.
     * 
     * https://vitejs.dev/guide/assets.html#the-public-directory
     */
    copyPublicDir: false,

    // Wipe the build dir on every build. Prevents stale resources.
    emptyOutDir: true,

    outDir: "../../public/build", // relative to root dir.

    // Output manifest so we can use it on the PHP side to lookup assets.
    manifest: true,

    // Main entrypoint.
    rollupOptions: {
      input: [
        path.resolve(__dirname, "resources/scripts/main.js"),
        //path.resolve(__dirname, "resources/styles/main.scss"),
      ],
    },
  },

  /**
   * If working on multiple projects, specify a unique port in package.json.
   * Change it there so PHP can also be aware of which port to find Vite on.
   */
  server: {
    host: '127.0.0.1',
    strictPort: true,
    port: process.env.VITE_PORT ?? 5173,
  },
});
