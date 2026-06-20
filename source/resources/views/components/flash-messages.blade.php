@if(session('success'))
<div class="mb-4 px-4 py-3 text-sm"
     style="background:rgba(52,211,153,0.1); border:1px solid rgba(52,211,153,0.3); color:#34d399; border-radius:2px;">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 px-4 py-3 text-sm"
     style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#ef4444; border-radius:2px;">
    {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 px-4 py-3 text-sm"
     style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#ef4444; border-radius:2px;">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
