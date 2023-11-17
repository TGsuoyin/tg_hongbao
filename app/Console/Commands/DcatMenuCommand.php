<?php

namespace App\Console\Commands;

use Dcat\Admin\Models\Menu;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Dcat\Admin\Models\Permission;
use Illuminate\Support\Facades\DB;

class DcatMenuCommand extends Command
{
    protected $signature = 'admin:menu';

    protected $description = 'backup or populate menu data';

    private $backupPath = '';

    public function handle()
    {
        $type = $this->ask(<<<TEXT
Please select an action type:
1: backup only
2: fill only
3: backup and fill
TEXT
        );

        $this->backupPath = storage_path('menu-backup.bak');

        switch ($type) {
            case 1: // backup only
                $this->backupMenu();
                break;
            case 2: // fill only
                $this->fillMenu();
                break;
            case 3: // backup and fill
                $this->backupAndFillMenu();
                break;
            default:
                echo 'Please enter the correct action type';
                break;
        }
    }

    /**
     * backup menu
     */
    private function backupMenu()
    {
        $res = file_put_contents($this->backupPath, serialize(optional(Menu::get())->toArray()));

        echo $res ? 'Backup succeeded' : 'Backup failed';
    }

    /**
     * fill menu
     */
    private function fillMenu()
    {
        $menu = unserialize(file_get_contents($this->backupPath));

        $permission = $this->generatePermissions($menu);

        Menu::truncate();
        Menu::insert($menu);

        Permission::truncate();
        Permission::insert($permission);

        DB::table('admin_permission_menu')->truncate();
        foreach ($permission as $item) {
            $query = DB::table('admin_permission_menu');
            $query->insert([
                'permission_id' => $item['id'],
                'menu_id'       => $item['id'],
            ]);
            if ($item['parent_id'] != 0) {
                $query->insert([
                    'permission_id' => $item['id'],
                    'menu_id'       => $item['parent_id'],
                ]);
            }
        }

        echo 'Filled successfully';
    }

    /**
     * Back up and populate the menu
     */
    private function backupAndFillMenu()
    {
        $this->backupMenu();
        $this->fillMenu();
    }

    /**
     * Generate permission data
     *
     * @param $menu
     *
     * @return array
     */
    private function generatePermissions($menu)
    {
        $permissions = [];
        foreach ($menu as $item) {
            $temp = [];

            $temp['id']         = $item['id'];
            $temp['name']       = $item['title'];
            $temp['slug']       = (string)Str::uuid();
            $temp['http_path']  = $this->getHttpPath($item['uri']);
            $temp['order']      = $item['order'];
            $temp['parent_id']  = $item['parent_id'];
            $temp['created_at'] = $item['created_at'];
            $temp['updated_at'] = $item['updated_at'];

            $permissions[] = $temp;
            unset($temp);
        }

        return $permissions;
    }

    /**
     * Get http path according to menu uri
     *
     * @param $uri
     *
     * @return string
     */
    private function getHttpPath($uri)
    {
        if ($uri == '/') {
            return '';
        }

        if ($uri == '') {
            return '';
        }

        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }

        return $uri . '*';
    }
}
