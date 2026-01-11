using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace FamilyTreeSystem.Migrations
{
    /// <inheritdoc />
    public partial class AddRelationshipTypes : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<int>(
                name: "FatherRelationType",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "MotherRelationType",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "SpouseRelationType",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.CreateTable(
                name: "ChildRelations",
                columns: table => new
                {
                    Id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    ParentId = table.Column<int>(type: "INTEGER", nullable: false),
                    ChildId = table.Column<int>(type: "INTEGER", nullable: false),
                    RelationshipType = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_ChildRelations", x => x.Id);
                    table.ForeignKey(
                        name: "FK_ChildRelations_FamilyMembers_ChildId",
                        column: x => x.ChildId,
                        principalTable: "FamilyMembers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_ChildRelations_FamilyMembers_ParentId",
                        column: x => x.ParentId,
                        principalTable: "FamilyMembers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "SiblingRelations",
                columns: table => new
                {
                    Id = table.Column<int>(type: "INTEGER", nullable: false)
                        .Annotation("Sqlite:Autoincrement", true),
                    Member1Id = table.Column<int>(type: "INTEGER", nullable: false),
                    Member2Id = table.Column<int>(type: "INTEGER", nullable: false),
                    RelationshipType = table.Column<int>(type: "INTEGER", nullable: false),
                    CreatedAt = table.Column<DateTime>(type: "TEXT", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_SiblingRelations", x => x.Id);
                    table.ForeignKey(
                        name: "FK_SiblingRelations_FamilyMembers_Member1Id",
                        column: x => x.Member1Id,
                        principalTable: "FamilyMembers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_SiblingRelations_FamilyMembers_Member2Id",
                        column: x => x.Member2Id,
                        principalTable: "FamilyMembers",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_ChildRelations_ChildId",
                table: "ChildRelations",
                column: "ChildId");

            migrationBuilder.CreateIndex(
                name: "IX_ChildRelations_ParentId",
                table: "ChildRelations",
                column: "ParentId");

            migrationBuilder.CreateIndex(
                name: "IX_SiblingRelations_Member1Id",
                table: "SiblingRelations",
                column: "Member1Id");

            migrationBuilder.CreateIndex(
                name: "IX_SiblingRelations_Member2Id",
                table: "SiblingRelations",
                column: "Member2Id");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "ChildRelations");

            migrationBuilder.DropTable(
                name: "SiblingRelations");

            migrationBuilder.DropColumn(
                name: "FatherRelationType",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "MotherRelationType",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "SpouseRelationType",
                table: "FamilyMembers");
        }
    }
}
