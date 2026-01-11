<div class="tree-node-container">
    <div class="tree-node {{ $member->gender }}" data-member-id="{{ $member->id }}">
        <span class="name">{{ $member->name }}</span>
        <div class="details">
            <div>{{ $member->gender_display }}</div>
            @if($member->ziwei)
                <div>{{ $member->ziwei->character }}</div>
            @endif
            @if($member->generation)
                <div>第{{ $member->generation }}代</div>
            @endif
        </div>
    </div>
    
    @if($member->children->count() > 0)
        <div class="tree-branch"></div>
        <div class="tree-children">
            @foreach($member->children as $child)
                <div class="tree-child">
                    <div class="tree-line"></div>
                    @include('family-members.partials.tree-node', ['member' => $child, 'level' => $level + 1])
                </div>
            @endforeach
        </div>
    @endif
</div>