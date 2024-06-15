<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{

    public function store(ContactCreateRequest $contactCreateRequest): ContactResource
    {
        $data = $contactCreateRequest->validated();
        $user = Auth::user();
        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();
        return new ContactResource($contact);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::where('user_id', $user->id)->where('id', $id)->first();
        if (!$contact) {
            throw new HttpResponseException(response([
                'errors' => [
                    'messages' => 'contact not found'
                ]
            ], 404));
        }
        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $contactUpdateRequest): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::where('user_id', $user->id)->where('id', $id)->first();
        if (!$contact) {
            throw new HttpResponseException(response([
                'errors' => [
                    'messages' => 'contact not found'
                ]
            ], 404));
        }
        $data = $contactUpdateRequest->validated();
        $contact->fill($data);
        $contact->save();
        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::where('user_id', $user->id)->where('id', $id)->first();
        if (!$contact) {
            throw new HttpResponseException(response([
                'errors' => [
                    'messages' => 'contact not found'
                ]
            ], 404));
        }
        $contact->delete();
        return response()->json(['data' => true])->setStatusCode(200);
    }

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $query = $request->input('query');

        $contacts = Contact::query()->where('user_id', $user->id);

        if ($query) {
            $contacts->whereAny(['first_name', 'last_name', 'email', 'phone'], 'LIKE', '%' . $query . '%');
        }

        $contacts = $contacts->paginate(perPage: $size, page: $page);
        return new ContactCollection($contacts);
    }
}
