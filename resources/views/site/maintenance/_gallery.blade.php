@if ($main->docs->count())
    <?php $doc_count = 0; ?>
    <div style="width: 100%; overflow: hidden;">
    @foreach ($main->docs as $doc)
        <div style="width: 60px; float: left; padding-right: 5px">
            <a href="{{ $doc->AttachmentUrl }}" class="html5lightbox " title="{{ $doc->name }}" data-lityXXX>
                <img src="{{ $doc->AttachmentUrl }}" class="thumbnail img-responsive img-thumbnail"></a>
        </div>
        <?php $doc_count ++; ?>
        @if ($doc_count == 10)
            <br>
        @endif
    @endforeach
</div>
    @else
    <div>No photos found<br><br></div>
@endif