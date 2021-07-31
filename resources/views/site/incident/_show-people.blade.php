{{-- Show People Details --}}
<div class="portlet light" id="show_people">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">Persons Involved</span>
        </div>
        <div class="actions">
            @if (Auth::user()->allowed2('edit.site.incident', $incident))
                {{-- <button class="btn btn-circle green btn-outline btn-sm" onclick="editForm('people')">Edit</button> --}}
                <a href="/site/incident/{{ $incident->id }}/people/create" class="btn btn-circle green btn-outline btn-sm">Add</a>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        @foreach ($incident->people as $person)
            <div class="row">
                <div class="col-xs-1"><a href="/site/incident/{{ $incident->id }}/people/{{ $person->id  }}"><i class="fa fa-search"></i></a></div>
                <div class="col-xs-4">{{ $person->typeName }}</div>
                <div class="col-xs-6">{{ $person->name }} @if ($person->employer) ({{ $person->employer }}) @endif </div>
                <div class="col-xs-1">@if ($person->status == 2) <i class="fa fa-check font-green"></i> @endif</div>
            </div>
            <hr class="field-hr">
        @endforeach
    </div>
</div>