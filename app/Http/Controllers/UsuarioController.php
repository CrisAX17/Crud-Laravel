<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();

       $data = [
        'usuarios' => $usuarios,
        'status' => 200
       ];

        return response()->json($usuarios, 200);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:9',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $usuarios = Usuario::create([
            'email' => $request->email,
            'password' => hash::make($request->password),
            'Active' => 1,
        ]);

        if (!$usuarios){
            $data = [
                'message' => 'Error al crear el usuario',
                'status' => 500
            ];
            return response()->json($data,500);
        }
        $data = [
            'usuario' => $usuarios,
            'status' => 201
        ];

        return response()->json($data, 201);

    }

    public function show($id)
    {
        $usuarios = Usuario::find($id);

        if (!$usuarios){
            $data = [
                'message' => 'El usuario no existe',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        $data = [
            'usuario' => $usuarios,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function destroy($id) 
    {
        $usuarios = Usuario::find($id);

        if (!$usuarios) {
            $data = [
                'message' => 'El usuario no existe',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        $usuarios->delete();

        $data = [
            'message' => 'El usuario fue eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        $usuarios = Usuario::find($id);

        if(!$usuarios) {
            $data = [
                'message' => 'El usuario no existe',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:9',
        ]);

        if($validator->fails()) {
            $data = [
                'message' => 'Error al actualizar el usuario',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $usuarios->email = $request->email;
        $usuarios->password = bcrypt($request->password);
        $usuarios->Active = $request->Active;

        $usuarios->save();

        $data = [
            'message' => 'Usuario actualizado',
            'usuario' => $usuarios,
            'status' => 200
        ];

        return response()->json($data,200);
    }

    public function updatePartial(Request $request, $id)
    {

        $usuarios = Usuario::find($id);

        if(!$usuarios) {
            $data = [
                'message' => 'El usuario no existe',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(),[
            'email' => 'email|unique:usuario',
            'password' => 'min:9',
        ]);

        if($validator->fails()){
            $data = [
                'message' => 'Error al actualizar el usuario',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data,400);
        }

        if ($request->has('email')){
            $usuarios->email = $request->email;
        }

        if ($request->has('password')){
            $usuarios->password = $request->password;
        }

        $usuarios->save();

        $data = [
            'message' => 'Usuario actualizado',
            'usuario' => $usuarios,
            'status' => 200
        ];

        return response()->json($data,200);
    }
}
