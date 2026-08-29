<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a dummy user
$user = new App\Models\User();
$user->id = 1;
$user->name = 'Admin';
$user->email = 'admin@admin.com';
$user->role = 'admin';
Auth::login($user);

$ajuanBpd = App\Models\AjuanBpd::with(['desa', 'pesertas.bpd', 'checklists.templateChecklist', 'milestones'])->find(14);
$html = view('admin.ajuan-bpd.show', compact('ajuanBpd'))->render();
file_put_contents('test_render.html', $html);
echo "Done\n";
