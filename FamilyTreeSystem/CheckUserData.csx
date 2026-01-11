using System;
using System.Data.SQLite;

// 连接字符串
//string connectionString = "Data Source=C:\\Users\\54143\\AppData\\Local\\familytree.db";
string connectionString = "Data Source=familytree.db";

using (SQLiteConnection connection = new SQLiteConnection(connectionString))
{
    connection.Open();
    
    // 查询用户表数据
    string query = "SELECT * FROM Users";
    using (SQLiteCommand command = new SQLiteCommand(query, connection))
    using (SQLiteDataReader reader = command.ExecuteReader())
    {
        Console.WriteLine("Users表数据：");
        Console.WriteLine("Id | Name | Email | CreatedAt");
        Console.WriteLine("----------------------------------------");
        
        if (!reader.HasRows)
        {
            Console.WriteLine("没有用户数据！");
        }
        else
        {
            while (reader.Read())
            {
                int id = reader.GetInt32(0);
                string name = reader.GetString(1);
                string email = reader.GetString(2);
                DateTime createdAt = reader.GetDateTime(6);
                
                Console.WriteLine($"{id} | {name} | {email} | {createdAt}");
            }
        }
    }
    
    // 查询用户表结构
    query = "PRAGMA table_info(Users)";
    using (SQLiteCommand command = new SQLiteCommand(query, connection))
    using (SQLiteDataReader reader = command.ExecuteReader())
    {
        Console.WriteLine("\nUsers表结构：");
        Console.WriteLine("cid | name | type | notnull | dflt_value | pk");
        Console.WriteLine("------------------------------------------------");
        
        while (reader.Read())
        {
            Console.WriteLine($"{reader.GetInt32(0)} | {reader.GetString(1)} | {reader.GetString(2)} | {reader.GetInt32(3)} | {reader[4]} | {reader.GetInt32(5)}");
        }
    }
}

Console.WriteLine("\n按任意键退出...");
Console.ReadKey();