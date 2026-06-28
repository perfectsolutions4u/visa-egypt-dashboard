<a href="{{ route('dashboard.memberships.show', $id) }}" title="View">
    <i class="fa fa-eye"></i>
</a>

<a href="{{ route('dashboard.memberships.edit', $id) }}" title="Edit">
    <i class="fa fa-edit"></i>
</a>

<a data-delete-url="{{ route('dashboard.memberships.destroy', $id) }}" href="javascript:;"
   type="button" class="btn-delete-resource-modal" data-bs-toggle="modal" data-bs-target="#deleteResourceModal" title="Delete">
    <i class="fa fa-trash"></i>
</a>
