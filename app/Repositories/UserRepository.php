<?php

namespace App\Repositories;

use App\User;

class UserRepository implements RepositoryInterface
{
    /**
     * @var User
     */
    protected $user;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function all()
    {
        return $this->user->all();
    }

    public function create(array $data)
    {
        return $this->user->create($data);
    }

    /**
     *
     * @param $request
     */
    public function update($data, $id)
    {
        return $this->user->find($id)->update($data);
    }

    public function delete($id)
    {
        return $this->user->find($id)->delete();
    }

    public function show($id)
    {
        return $this->user->find($id);
    }

    public function findByConfirmCode($code)
    {
        return $this->user->whereConfirmCode($code)->first();
    }
}
